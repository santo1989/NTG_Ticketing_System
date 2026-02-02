<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class UsersTableSeeder extends Seeder
{

    public function run()
    {
        $commonPassword = bcrypt('12345678');
        $passwordText = '12345678';

        // Admin user
        User::create([
            'role_id' => 1,
            'name' => 'Admin User',
            'emp_id' => '0001',
            'email' => 'admin@ntg.com.bd',
            'email_verified_at' => now(),
            'picture' => 'avatar.png',
            'company_id' => '1',
            'department_id' => '9',
            'designation_id' => '10',
            'password' => $commonPassword,
            'password_text' => $passwordText,
            'remember_token' => Str::random(10),
        ]);

        // General / Client user
        User::create([
            'role_id' => 2,
            'name' => 'General User',
            'emp_id' => '0002',
            'email' => 'general@ntg.com.bd',
            'email_verified_at' => now(),
            'picture' => 'avatar.png',
            'company_id' => '1',
            'department_id' => '15',
            'designation_id' => '6',
            'mobile' => '01800000002',
            'password' => $commonPassword,
            'password_text' => $passwordText,
            'remember_token' => Str::random(10),
        ]);

        // Supervisor user
        User::create([
            'role_id' => 3,
            'name' => 'Supervisor User',
            'emp_id' => '0003',
            'email' => 'supervisor@ntg.com.bd',
            'email_verified_at' => now(),
            'picture' => 'avatar.png',
            'company_id' => '1',
            'department_id' => '9',
            'designation_id' => '11',
            'mobile' => '01800000003',
            'password' => $commonPassword,
            'password_text' => $passwordText,
            'remember_token' => Str::random(10),
        ]);

        // Support team user
        User::create([
            'role_id' => 4,
            'name' => 'Support User',
            'emp_id' => '0004',
            'email' => 'support@ntg.com.bd',
            'email_verified_at' => now(),
            'picture' => 'avatar.png',
            'company_id' => '1',
            'department_id' => '15',
            'designation_id' => '6',
            'mobile' => '01800000004',
            'password' => $commonPassword,
            'password_text' => $passwordText,
            'remember_token' => Str::random(10),
        ]);

        // Client user (explicit)
        User::create([
            'role_id' => 5,
            'name' => 'Client User',
            'emp_id' => '0005',
            'email' => 'client@ntg.com.bd',
            'email_verified_at' => now(),
            'picture' => 'avatar.png',
            'company_id' => '1',
            'department_id' => '15',
            'designation_id' => '6',
            'mobile' => '01800000005',
            'password' => $commonPassword,
            'password_text' => $passwordText,
            'remember_token' => Str::random(10),
        ]);
    }
}
