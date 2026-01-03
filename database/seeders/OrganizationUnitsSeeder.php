<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationUnitsSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $units = [
            ['code' => 'RGD', 'name' => 'Registrar General Directorate', 'type' => 'directorate', 'parent' => null],
            ['code' => 'REG', 'name' => 'Registry Operations', 'type' => 'department', 'parent' => 'RGD'],
            ['code' => 'COMPL', 'name' => 'Compliance & Inspection', 'type' => 'department', 'parent' => 'RGD'],
            ['code' => 'LEGAL', 'name' => 'Legal Services', 'type' => 'department', 'parent' => 'RGD'],
            ['code' => 'IT', 'name' => 'Information Technology', 'type' => 'department', 'parent' => 'RGD'],
            ['code' => 'FIN', 'name' => 'Finance & Accounts', 'type' => 'department', 'parent' => 'RGD'],
            ['code' => 'CS', 'name' => 'Customer Service', 'type' => 'department', 'parent' => 'RGD'],
        ];

        // Ensure parents are created first
        foreach ($units as $unit) {
            if ($unit['parent'] !== null) {
                continue;
            }
            DB::table('organization_units')->updateOrInsert(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'type' => $unit['type'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        // Then children referencing parents
        foreach ($units as $unit) {
            if ($unit['parent'] === null) {
                continue;
            }
            $parentId = DB::table('organization_units')->where('code', $unit['parent'])->value('id');

            DB::table('organization_units')->updateOrInsert(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'type' => $unit['type'],
                    'parent_id' => $parentId,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
