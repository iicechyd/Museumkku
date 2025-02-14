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
            'name' => 'นายพันเทพ เด่นดัง',
            'password' => 'super123',
            'is_approved' => '1',
        ]);
        
        User::create([
            'role_id' => '2',
            'email' => 'admin@gmail.com',
            'name' => 'นางสาวใจดี เด่นกล้า',
            'password' => 'admin123',
            'is_approved' => '1',
        ]);

        User::create([
            'role_id' => '2',
            'email' => 'admin2@gmail.com',
            'name' => 'นายเทสดี ก้องไกล',
            'password' => 'admin123',
            'is_approved' => '1',
        ]);

        User::create([
            'role_id' => '3',
            'email' => 'executive@gmail.com',
            'name' => 'นายประธาน บริษัท',
            'password' => 'executive123',
            'is_approved' => '1',
        ]);
    }
}
