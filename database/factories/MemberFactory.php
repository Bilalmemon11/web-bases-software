<?php 
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->numerify('03' . $this->faker->numerify('#########')),
            'cnic' => $this->faker->numerify('#############'),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
