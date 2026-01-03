<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $types = [
            ['key' => 'memo', 'name' => 'Memo', 'description' => 'Internal memorandum'],
            ['key' => 'letter', 'name' => 'Letter', 'description' => 'Official letter'],
            ['key' => 'report', 'name' => 'Report', 'description' => 'Departmental report'],
            ['key' => 'invoice', 'name' => 'Invoice', 'description' => 'Vendor invoice'],
        ];

        foreach ($types as $t) {
            DB::table('document_types')->updateOrInsert(
                ['key' => $t['key']],
                [
                    'name' => $t['name'],
                    'description' => $t['description'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
