<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificationsSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $rows = [
            ['key' => 'public', 'name' => 'Public', 'description' => 'Accessible to all users', 'clearance_level' => 1, 'is_active' => true],
            ['key' => 'private', 'name' => 'Private', 'description' => 'Internal access only', 'clearance_level' => 1, 'is_active' => true],
            ['key' => 'confidential', 'name' => 'Confidential', 'description' => 'Restricted to authorized staff', 'clearance_level' => 2, 'is_active' => true],
            ['key' => 'restricted', 'name' => 'Restricted', 'description' => 'Strictly controlled access', 'clearance_level' => 3, 'is_active' => true],
            ['key' => 'secret', 'name' => 'Secret', 'description' => 'High sensitivity; limited to select roles', 'clearance_level' => 4, 'is_active' => true],
            ['key' => 'top_secret', 'name' => 'Top Secret', 'description' => 'Highest sensitivity; exceptional access only', 'clearance_level' => 5, 'is_active' => true],
        ];

        foreach ($rows as $row) {
            DB::table('classifications')->updateOrInsert(
                ['key' => $row['key']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'clearance_level' => $row['clearance_level'],
                    'is_active' => $row['is_active'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
