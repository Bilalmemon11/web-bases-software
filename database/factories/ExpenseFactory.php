<?php 
namespace Database\Factories;

use App\Models\PredefinedCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => null,
            'category' => PredefinedCategory::inRandomOrder()->first()->name,
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 5000, 500000),
            'expense_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}

