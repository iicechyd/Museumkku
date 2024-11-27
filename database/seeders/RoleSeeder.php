<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'role_id' => 1,
            'role_name' => 'Super Admin'
        ]);
        Role::create([
            'role_id' => 2,
            'role_name' => 'Admin',
        ]);
        Role::create([
            'role_id' => 3,
            'role_name' => 'Executive',
        ]);
    }
}
