<?php 
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Client;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['available']);
        return [
            'project_id' => null,
            'unit_no' => 'U-' . $this->faker->unique()->numerify('###'),
            'type' => $this->faker->randomElement(['Plot', 'Flat', 'Shop']),
            'size' => $this->faker->randomElement(['5 Marla', '10 Marla', '1 Kanal']),
            'cost_price' => $this->faker->randomFloat(2, 500000, 5000000),
            'sale_price' => $this->faker->optional()->randomFloat(2, 600000, 7000000),
            'status' => $status,
        ];
    }
}
