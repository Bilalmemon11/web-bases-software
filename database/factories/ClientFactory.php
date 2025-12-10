<?php 
namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => null,
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'cnic' => $this->faker->numerify('#####-#######-#'),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

