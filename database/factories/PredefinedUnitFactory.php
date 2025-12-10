<?php 
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PredefinedUnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['Plot', 'Flat', 'Shop', 'House', 'Office', 'Warehouse', 'Studio']),
            'size' => $this->faker->randomElement(['5 Marla', '10 Marla', '1 Kanal', '2 Kanal', '500 Sq Ft', '1000 Sq Ft', '1500 Sq Ft', '2000 Sq Ft', '2500 Sq Ft']),
            'cost_price' => $this->faker->randomFloat(2, 500000, 5000000, 50000),
            'default_sale_price' => $this->faker->optional()->randomFloat(2, 600000, 7000000, 500000, 5000000),
        ];
    }
}
