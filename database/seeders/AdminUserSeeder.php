<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $adminRoleId = DB::table('roles')->where('key', 'admin')->value('id');
        if (!$adminRoleId) {
            // Ensure roles exist
            $this->call(RolesSeeder::class);
            $adminRoleId = DB::table('roles')->where('key', 'admin')->value('id');
        }

        // Ensure base org units exist
        $rgdId = DB::table('organization_units')->where('code', 'RGD')->value('id');
        if (!$rgdId) {
            $this->call(OrganizationUnitsSeeder::class);
            $rgdId = DB::table('organization_units')->where('code', 'RGD')->value('id');
        }

        $email = 'admin@orc.local';

        $exists = DB::table('users')->where('email', $email)->exists();
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'System Administrator',
                'email' => $email,
                'password' => Hash::make('Admin@12345'),
                'role_id' => $adminRoleId,
                'organization_unit_id' => $rgdId,
                'status' => 'active',
                'clearance_level' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
