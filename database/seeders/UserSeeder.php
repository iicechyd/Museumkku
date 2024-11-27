<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::create([
            'role_id' => '1',
            'email' => 'superadmin@gmail.com',
            'name' => 'superadmin',
            'password' => 'super123',
            'is_approved' => '1',
        ]);
        
        User::create([
            'role_id' => '2',
            'email' => 'admin@gmail.com',
            'name' => 'admin',
            'password' => 'admin123',
            'is_approved' => '2',
        ]);

    }
}
