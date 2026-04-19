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
         $units = [
            ['type' => 'Plot', 'size' => '5 Marla', 'cost_price' => 1500000, 'default_sale_price' => 1800000],
            ['type' => 'Plot', 'size' => '10 Marla', 'cost_price' => 2500000, 'default_sale_price' => 2800000],
            ['type' => 'House', 'size' => '1 Kanal', 'cost_price' => 4500000, 'default_sale_price' => 5200000],
            ['type' => 'Flat', 'size' => '1000 Sq Ft', 'cost_price' => 2000000, 'default_sale_price' => 2300000],
            ['type' => 'Shop', 'size' => '500 Sq Ft', 'cost_price' => 1000000, 'default_sale_price' => 1200000],
            ['type' => 'Office', 'size' => '1500 Sq Ft', 'cost_price' => 2500000, 'default_sale_price' => 2800000],
            ['type' => 'Studio', 'size' => '500 Sq Ft', 'cost_price' => 800000, 'default_sale_price' => 950000],
        ];

        foreach ($units as $unit) {
            PredefinedUnit::create($unit);
        }
        // $this->call(ProjectSeeder::class);
    }
}
