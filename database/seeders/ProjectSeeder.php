<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Project, Member, Expense, Unit, Client, PredefinedUnit};
use Database\Factories\PredefinedUnitFactory;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::factory(20)->create(); // global member pool

        Project::factory(5)->create()->each(function ($project) use ($members) {
            // Randomly select members for this project
            $assignedMembers = $members->random(rand(5, 10));

            // Step 1: Generate random investment amounts
            $investments = [];
            foreach ($assignedMembers as $member) {
                $investments[$member->id] = fake()->randomFloat(2, 50000, 5000000);
            }

            // Step 2: Calculate total investment
            $totalInvestment = array_sum($investments);

            // Step 3: Attach members with correct profit share
            foreach ($assignedMembers as $member) {
                $investmentAmount = $investments[$member->id];
                $profitShare = ($investmentAmount / $totalInvestment) * 100;

                $project->members()->attach($member->id, [
                    'investment_amount' => $investmentAmount,
                    'profit_share' => round($profitShare, 2),
                    'role' => $member->is_manager ? 'manager' : 'investor',
                ]);
            }

            // Optional: set project total investment for consistency
            $project->update(['total_investment' => $totalInvestment]);

            // Create related expenses, clients, and units
            Expense::factory(rand(10, 20))->create(['project_id' => $project->id]);

            $clients = Client::factory(rand(5, 10))->create();
            Unit::factory(rand(20, 40))->create([
                'project_id' => $project->id,
            ]);
        });
    }
}
