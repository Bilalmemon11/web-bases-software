<?php

namespace Database\Seeders;

use App\Models\PredefinedUnit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PredefinedCategorySeeder::class);
        $this->call(ManagerSeeder::class);
        PredefinedUnit::factory(7)->create();
        // $this->call(ProjectSeeder::class);
    }
}
