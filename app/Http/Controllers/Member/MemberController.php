<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Project $project)
    {
        $project->load(['members']);
        $members = $project->members()->paginate(10);
        $allMembers = Member::whereNotIn('id', function ($query) use ($project) {
            $query->select('member_id')
                ->from('project_members')
                ->where('project_id', $project->id);
        })->get();
        return view('members.index', compact('members', 'project', 'allMembers'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{11}$/|unique:members',
            'cnic' => 'nullable|string|regex:/^[0-9]{13}$/|unique:members',
            'investment_amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated, $project) {
            // Step 1: Create new member
            $member = Member::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'cnic' => $validated['cnic'] ?? null,
                'investment_amount' => $validated['investment_amount'],
            ]);

            // Step 2: Attach new member temporarily with 0 share
            $project->members()->attach($member->id, [
                'investment_amount' => $validated['investment_amount'],
                'profit_share' => 0,
                'role' => 'investor',
            ]);

            // Step 3: Update project's total investment
            $newTotalInvestment = $project->members()->sum('project_members.investment_amount');
            $project->update(['total_investment' => $newTotalInvestment]);

            // Step 4: Recalculate ALL members' shares based on new total
            $project->members()->each(function ($m) use ($newTotalInvestment) {
                $share = ($m->pivot->investment_amount / $newTotalInvestment) * 100;
                $m->pivot->profit_share = round($share, 2);
                $m->pivot->save();
            });
        });

        return redirect()
            ->route('members.index', $project->slug)
            ->with('success', 'Member added and project total investment updated.');
    }

    public function update(Request $request, Project $project, Member $member)
    {
        // Check if member belongs to this project
        if (!$project->members()->where('member_id', $member->id)->exists()) {
            return redirect()
                ->route('members.index', ['project' => $project->slug, 'openpopup' => 'editMemberModal-' . $member->id])
                ->with('error', 'Member not found in this project.')
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'edit_name' => 'required|string|max:255',
                'edit_phone' => 'required|string|regex:/^[0-9]{11}$/|unique:members,phone,' . $member->id,
                'edit_cnic' => 'nullable|string|regex:/^[0-9]{13}$/|unique:members,cnic,' . $member->id,
                'edit_investment_amount' => 'required|numeric|min:1',
            ]);

            DB::transaction(function () use ($validated, $project, $member) {
                // Step 1: Update member's basic info
                $member->update([
                    'name' => $validated['edit_name'],
                    'phone' => $validated['edit_phone'],
                    'cnic' => $validated['edit_cnic'] ?? null,
                    'investment_amount' => $validated['edit_investment_amount'],
                ]);

                // Step 2: Update pivot investment amount
                $project->members()->updateExistingPivot($member->id, [
                    'investment_amount' => $validated['edit_investment_amount'],
                ]);

                // Step 3: Recalculate project's total investment
                $newTotalInvestment = $project->members()->sum('project_members.investment_amount');
                $project->update(['total_investment' => $newTotalInvestment]);

                // Step 4: Recalculate ALL members' shares
                $project->members()->each(function ($m) use ($newTotalInvestment) {
                    $share = ($m->pivot->investment_amount / $newTotalInvestment) * 100;
                    $m->pivot->profit_share = round($share, 2);
                    $m->pivot->save();
                });
            });

            return redirect()
                ->route('members.index', $project->slug)
                ->with('success', 'Member updated successfully and shares recalculated.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('members.index', ['project' => $project->slug, 'openpopup' => 'editMemberModal-' . $member->id])
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('members.index', ['project' => $project->slug, 'openpopup' => 'editMemberModal-' . $member->id])
                ->with('error', 'An error occurred while updating the member.')
                ->withInput();
        }
    }

    public function addFromList(Request $request, Project $project)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'integer',
                'exists:members,id',
                // Validate member is not already in project
                function ($attribute, $value, $fail) use ($project) {
                    if ($project->members()->where('member_id', $value)->exists()) {
                        $fail('This member is already part of the project.');
                    }
                },
            ],
            'investment_amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated, $project) {

            // Step 1: Attach new member temporarily with 0 share
            $project->members()->attach($validated['member_id'], [
                'investment_amount' => $validated['investment_amount'],
                'profit_share' => 0,
                'role' => 'investor',
            ]);

            // Step 2: Update project's total investment
            $newTotalInvestment = $project->members()->sum('project_members.investment_amount');
            $project->update(['total_investment' => $newTotalInvestment]);

            // Step 3: Recalculate ALL members' shares based on new total
            $project->members()->each(function ($m) use ($newTotalInvestment) {
                $share = ($m->pivot->investment_amount / $newTotalInvestment) * 100;
                $m->pivot->profit_share = round($share, 2);
                $m->pivot->save();
            });
        });

        return redirect()
            ->route('members.index', $project->slug)
            ->with('success', 'Member added and project total investment updated.');
    }

    public function destroy(Project $project, Member $member)
    {
        // Check if member belongs to this project
        if (!$project->members()->where('member_id', $member->id)->exists()) {
            return redirect()
                ->route('members.index', $project->slug)
                ->with('error', 'Member not found in this project.');
        }

        // Prevent deleting manager
        $pivot = $project->members()->where('member_id', $member->id)->first()->pivot;
        if ($pivot->role === 'manager') {
            return redirect()
                ->route('members.index', $project->slug)
                ->with('error', 'Cannot remove the project manager.');
        }

        DB::transaction(function () use ($project, $member) {
            // Step 1: Detach member from project (removes from pivot table only)
            $project->members()->detach($member->id);

            // Step 2: Recalculate project's total investment
            $newTotalInvestment = $project->members()->sum('project_members.investment_amount');
            $project->update(['total_investment' => $newTotalInvestment]);

            // Step 3: Recalculate remaining members' shares
            if ($newTotalInvestment > 0) {
                $project->members()->each(function ($m) use ($newTotalInvestment) {
                    $share = ($m->pivot->investment_amount / $newTotalInvestment) * 100;
                    $m->pivot->profit_share = round($share, 2);
                    $m->pivot->save();
                });
            }
        });

        return redirect()
            ->route('members.index', $project->slug)
            ->with('success', 'Member removed from project successfully.');
    }
}
