<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::create([
            'name' => 'Junaid Manager',
            'phone' => '01234567890',
            'cnic' => '1234567890123',
            'address' => 'Head Office',
            'notes' => 'Junaid Manager Notes',
            'is_manager' => true,
        ]);
    }
}
