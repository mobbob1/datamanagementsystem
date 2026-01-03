<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $roles = [
            ['key' => 'admin', 'name' => 'Administrator'],
            ['key' => 'registrar', 'name' => 'Registrar General Director'],
            ['key' => 'registry', 'name' => 'Registry Officer'],
            ['key' => 'dept_head', 'name' => 'Department Head/Lead'],
            ['key' => 'staff', 'name' => 'Staff User'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['key' => $role['key']],
                ['name' => $role['name'], 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
