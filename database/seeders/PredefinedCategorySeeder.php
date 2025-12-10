<?php

namespace Database\Seeders;

use App\Models\PredefinedCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PredefinedCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categories = [
            'Material',
            'Labor',
            'Transport',
            'Electricity',
            'Misc'
        ];
        foreach ($categories as $category) {
            PredefinedCategory::create([
                'name' => $category,
            ]);
        }
    }
}
