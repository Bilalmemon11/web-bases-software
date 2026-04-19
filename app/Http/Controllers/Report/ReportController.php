<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Project $project)
    {
        return view('reports.index', compact('project'));
    }

    public function members(Project $project)
    {
        $members = $project->members()
            ->withPivot('investment_amount', 'profit_share', 'role')
            ->get();

        $totalInvestment = $members->sum('pivot.investment_amount');

        return view('reports.members', compact('project', 'members', 'totalInvestment'));
    }

    public function clients(Project $project)
    {
        $clients = $project->sales()
            ->with('client')
            ->get()
            ->pluck('client')
            ->unique('id');

        $clientStats = $clients->map(function ($client) use ($project) {
            $sales = $client->sales()->where('project_id', $project->id)->get();
            return [
                'client' => $client,
                'total_sales' => $sales->sum('total_amount'),
                'total_paid' => $sales->sum('paid_amount'),
                'total_discount' => $sales->sum('discount'),
                'total_pending' => $sales->sum('pending_amount'),
                'units_count' => $sales->flatMap->units->count(),
            ];
        });

        return view('reports.clients', compact('project', 'clientStats'));
    }

    public function units(Project $project)
    {
        $units = $project->units()->with(['sales.client'])->get();

        $stats = [
            'total' => $units->count(),
            'available' => $units->where('status', 'available')->count(),
            'reserved' => $units->where('status', 'reserved')->count(),
            'sold' => $units->where('status', 'sold')->count(),
            'by_type' => $units->groupBy('type')->map->count(),
            'total_value' => $units->sum('sale_price'),
            'sold_value' => $units->where('status', 'sold')->sum('sale_price'),
        ];

        return view('reports.units', compact('project', 'units', 'stats'));
    }

    public function sales(Project $project)
    {
        $sales = $project->sales()
            ->with(['client', 'units', 'payments'])
            ->orderBy('sale_date', 'desc')
            ->get();

        $stats = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_received' => $sales->sum('paid_amount'),
            'total_discount' => $sales->sum('discount'),
            'total_pending' => $sales->sum('pending_amount'),
            'by_status' => $sales->groupBy('status')->map->count(),
            'by_month' => $sales->groupBy(fn($s) => $s->sale_date->format('M Y'))->map->sum('total_amount'),
        ];

        return view('reports.sales', compact('project', 'sales', 'stats'));
    }

    public function expenses(Project $project)
    {
        $expenses = $project->expenses()
            ->orderBy('expense_date', 'desc')
            ->get();

        $stats = [
            'total_expenses' => $expenses->sum('amount'),
            'land_cost' => $project->land_cost ?? 0,
            'grand_total' => $expenses->sum('amount') + ($project->land_cost ?? 0),
            'by_category' => $expenses->groupBy('category')->map->sum('amount'),
            'by_month' => $expenses->groupBy(fn($e) => $e->expense_date->format('M Y'))->map->sum('amount'),
        ];

        return view('reports.expenses', compact('project', 'expenses', 'stats'));
    }

    protected function getOverallData(Project $project)
    {
        return [
            'project' => $project,
            'members' => $project->members,
            'units_count' => $project->units()->count(),
            'sales_count' => $project->sales()->count(),
            'expenses_count' => $project->expenses()->count(),
            'clients_count' => $project->sales()->distinct('client_id')->count(),

            // Financial Summary
            'total_investment' => $project->total_investment,
            'total_expenses' => $project->total_expenses,
            'total_sales' => $project->total_sales,
            'total_received' => $project->total_received,
            'total_pending' => $project->total_pending,
            'total_discount' => $project->sales()->sum('discount'),
            'profit' => $project->profit,
            'progress' => $project->progress,

            // Units breakdown
            'units_available' => $project->units()->where('status', 'available')->count(),
            'units_reserved' => $project->units()->where('status', 'reserved')->count(),
            'units_sold' => $project->units()->where('status', 'sold')->count(),
        ];
    }

    /**
     * Normal overall view (for web)
     */
    public function overall(Project $project)
    {
        $data = $this->getOverallData($project);
        return view('reports.overall', $data);
    }

    public function downloadPdf(Project $project, $type)
    {
        $data = [];
        $view = '';

        switch ($type) {
            case 'members':
                $data = [
                    'project' => $project,
                    'members' => $project->members()->withPivot('investment_amount', 'profit_share', 'role')->get(),
                ];
                $view = 'reports.pdf.members';
                break;

            case 'clients':
                $clients = $project->sales()->with('client')->get()->pluck('client')->unique('id');
                $data = [
                    'project' => $project,
                    'clients' => $clients,
                ];
                $view = 'reports.pdf.clients';
                break;

            case 'units':
                $data = [
                    'project' => $project,
                    'units' => $project->units()->with(['sales.client'])->get(),
                ];
                $view = 'reports.pdf.units';
                break;

            case 'sales':
                $data = [
                    'project' => $project,
                    'sales' => $project->sales()->with(['client', 'units'])->get(),
                ];
                $view = 'reports.pdf.sales';
                break;

            case 'expenses':
                $data = [
                    'project' => $project,
                    'expenses' => $project->expenses()->get(),
                ];
                $view = 'reports.pdf.expenses';
                break;

            case 'overall':
                $data = $this->getOverallData($project);
                $view = 'reports.pdf.overall';
                break;
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->download("{$project->name}-{$type}-report-" . date('Y-m-d') . ".pdf");
    }

    /**
     * Single Client Report
     */
    public function singleClient(Project $project, Client $client)
    {
        // Ensure client belongs to this project
        if ($client->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load sales for this project only
        $client->load(['sales' => function ($query) use ($project) {
            $query->where('project_id', $project->id)
                ->with(['units', 'payments'])
                ->orderBy('sale_date', 'desc');
        }]);

        // Calculate summary
        $summary = [
            'total_sales' => $client->sales->sum('total_amount'),
            'total_discount' => $client->sales->sum('discount'),
            'net_amount' => $client->sales->sum('total_amount') - $client->sales->sum('discount'),
            'total_paid' => $client->sales->sum('paid_amount'),
            'total_pending' => $client->sales->sum('pending_amount'),
            'total_units' => $client->sales->flatMap->units->count(),
            'sales_count' => $client->sales->count(),
        ];

        return view('reports.single-client', compact('project', 'client', 'summary'));
    }

    public function downloadClientPdf(Project $project, Client $client)
    {
        // Ensure client belongs to this project
        if ($client->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        $client->load(['sales' => function ($query) use ($project) {
            $query->where('project_id', $project->id)
                ->with(['units', 'payments'])
                ->orderBy('sale_date', 'desc');
        }]);

        $summary = [
            'total_sales' => $client->sales->sum('total_amount'),
            'total_discount' => $client->sales->sum('discount'),
            'net_amount' => $client->sales->sum('total_amount') - $client->sales->sum('discount'),
            'total_paid' => $client->sales->sum('paid_amount'),
            'total_pending' => $client->sales->sum('pending_amount'),
            'total_units' => $client->sales->flatMap->units->count(),
            'sales_count' => $client->sales->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.single-client', compact('project', 'client', 'summary'));
        return $pdf->download("client-{$client->name}-report-" . date('Y-m-d') . ".pdf");
    }

    /**
     * Single Sale Report
     */
    public function singleSale(Project $project, Sale $sale)
    {
        // Ensure sale belongs to this project
        if ($sale->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        $sale->load(['client', 'units', 'payments']);

        return view('reports.single-sale', compact('project', 'sale'));
    }

    public function downloadSalePdf(Project $project, Sale $sale)
    {
        // Ensure sale belongs to this project
        if ($sale->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load all necessary relationships
        $sale->load(['client', 'units', 'payments', 'installments']);

        // Generate PDF
        $pdf = Pdf::loadView('reports.pdf.single-sale', compact('project', 'sale'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        $filename = "sale-{$sale->id}-{$sale->client->name}-" . date('Y-m-d') . ".pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Single Member Report
     */
    public function singleMember(Project $project, Member $member)
    {
        // Check if member is part of this project
        $projectMember = $project->members()->where('members.id', $member->id)->first();
        
        if (!$projectMember) {
            abort(403, 'Member not found in this project.');
        }

        // Get member's investment details for this project
        $investment = [
            'amount' => $projectMember->pivot->investment_amount,
            'profit_share' => $projectMember->pivot->profit_share,
            'role' => $projectMember->pivot->role,
        ];

        // Calculate potential profit based on project profit
        $projectProfit = $project->profit;
        $memberShare = ($investment['profit_share'] / 100) * $projectProfit;

        // Get all member's projects
        $allProjects = $member->projects()
            ->withPivot('investment_amount', 'profit_share', 'role')
            ->get();

        $summary = [
            'total_investment_all_projects' => $allProjects->sum('pivot.investment_amount'),
            'projects_count' => $allProjects->count(),
            'current_project_investment' => $investment['amount'],
            'expected_share' => $memberShare,
        ];

        return view('reports.single-member', compact('project', 'member', 'investment', 'summary', 'allProjects'));
    }

    public function downloadMemberPdf(Project $project, Member $member)
    {
        $projectMember = $project->members()->where('members.id', $member->id)->first();
        
        if (!$projectMember) {
            abort(403, 'Member not found in this project.');
        }

        $investment = [
            'amount' => $projectMember->pivot->investment_amount,
            'profit_share' => $projectMember->pivot->profit_share,
            'role' => $projectMember->pivot->role,
        ];

        $projectProfit = $project->profit;
        $memberShare = ($investment['profit_share'] / 100) * $projectProfit;

        $allProjects = $member->projects()
            ->withPivot('investment_amount', 'profit_share', 'role')
            ->get();

        $summary = [
            'total_investment_all_projects' => $allProjects->sum('pivot.investment_amount'),
            'projects_count' => $allProjects->count(),
            'current_project_investment' => $investment['amount'],
            'expected_share' => $memberShare,
        ];

        $pdf = Pdf::loadView('reports.pdf.single-member', compact('project', 'member', 'investment', 'summary', 'allProjects'));
        return $pdf->download("member-{$member->name}-report-" . date('Y-m-d') . ".pdf");
    }
}
