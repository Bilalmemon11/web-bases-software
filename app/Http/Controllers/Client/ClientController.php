<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request, Project $project)
    {
        // Only get clients that belong to this project
        $query = Client::where('project_id', $project->id);

        // 🔹 Filter: clients who have sales in this project
        if ($request->get('has_sales') === 'yes') {
            $query->whereHas('sales', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            });
        }

        // 🔹 Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('cnic', 'like', "%{$request->search}%")
                    ->orWhere('address', 'like', "%{$request->search}%");
            });
        }

        $clients = $query->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('clients.index', compact('project', 'clients'));
    }


    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|regex:/^[0-9]{11}$/|unique:clients,phone,NULL,id,project_id,' . $project->id,
            'cnic' => 'nullable|string|regex:/^[0-9]{13}$/|unique:clients,cnic,NULL,id,project_id,' . $project->id,
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Add project_id to the validated data
        $validated['project_id'] = $project->id;

        Client::create($validated);

        return redirect()->back()
            ->with('success', 'Client added successfully.');
    }

    public function update(Request $request, Project $project, Client $client)
    {
        // Ensure client belongs to this project
        if ($client->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'phone' => 'nullable|string|max:20|unique:clients,phone,' . $client->id . ',id,project_id,' . $project->id,
            'cnic' => "nullable|string|max:20|unique:clients,cnic,{$client->id},id,project_id," . $project->id,
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $client->update($validated);

        return redirect()->back()
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Project $project, Client $client)
    {
        // Ensure client belongs to this project
        if ($client->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the client has sales (in this project)
        if ($client->sales()->where('project_id', $project->id)->exists()) {
            $sales = $client->sales()->where('project_id', $project->id)->get();
            $salesInfo = $sales->map(fn($sale) => "Sale ID: {$sale->id}")->implode('; ');

            return redirect()->back()
                ->with('error', "Cannot delete client '{$client->name}' because they have existing sales: {$salesInfo}");
        }

        // Otherwise, safe to delete
        $client->delete();

        return redirect()
            ->route('clients.index', $project->slug)
            ->with('success', "Client '{$client->name}' deleted successfully.");
    }


    public function show(Project $project, Client $client)
    {
        // Ensure client belongs to this project
        if ($client->project_id !== $project->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load only sales and units that belong to this specific project
        $client->load(['sales' => function ($query) use ($project) {
            $query->where('project_id', $project->id)
                ->with('units');
        }]);

        // Calculate project-specific summary
        $summary = [
            'total_sales' => $client->sales->sum('total_amount'),
            'total_discount' => $client->sales->sum('discount'),
            'total_paid' => $client->sales->sum('paid_amount'),
            'total_pending' => $client->sales->sum(fn($s) => $s->total_amount - $s->discount - $s->paid_amount),
            'total_units' => $client->sales->flatMap->units->count(),
            'avg_unit_price' => $client->sales->flatMap->units->avg('pivot.unit_price') ?? 0,
        ];

        return view('clients.show', compact('project', 'client', 'summary'));
    }
}