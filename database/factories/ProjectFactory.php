<?php 
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->streetName() . ' Project',
            'description' => $this->faker->paragraph(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
            'total_investment' => $this->faker->randomFloat(2, 1000000, 50000000),
            'land_cost' => $this->faker->randomFloat(2, 500000, 5000000),
            'sale_price' => $this->faker->optional()->randomFloat(2, 5000000, 50000000),
            'status' => $this->faker->randomElement(['active', 'completed', 'archived','on_hold']),
        ];
    }
}
