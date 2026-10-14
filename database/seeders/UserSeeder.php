<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
                $users = [
            [
                'name'       => 'Jane FrontDesk',
                'email'      => 'frontdesk@blulane.com',
                'password'   => 'password',
                'role'       => 'frontdesk',
                'department' => 'Front Desk',
                'phone'      => '+63 917 000 0001',
                'shift'      => 'morning',
            ],
            [
                'name'       => 'Mark Supervisor',
                'email'      => 'supervisor@blulane.com',
                'password'   => 'password',
                'role'       => 'supervisor',
                'department' => 'Supervisor',
                'phone'      => '+63 917 000 0002',
                'shift'      => 'morning',
            ],
            [
                'name'       => 'Maria Housekeeping',
                'email'      => 'maria.housekeeping@blulane.com',
                'password'   => 'password',
                'role'       => 'staff',
                'department' => 'Housekeeping',
                'phone'      => '+63 917 000 0003',
                'shift'      => 'morning',
            ],
            [
                'name'       => 'Carlos Maintenance',
                'email'      => 'carlos.maintenance@blulane.com',
                'password'   => 'password',
                'role'       => 'staff',
                'department' => 'Maintenance',
                'phone'      => '+63 917 000 0004',
                'shift'      => 'evening',
            ],
            [
                'name'       => 'Sam IT',
                'email'      => 'sam.it@blulane.com',
                'password'   => 'password',
                'role'       => 'staff',
                'department' => 'IT',
                'phone'      => '+63 917 000 0005',
                'shift'      => 'morning',
            ],
            [
                'name'       => 'Alice FrontDesk',
                'email'      => 'alice.frontdesk@blulane.com',
                'password'   => 'password',
                'role'       => 'staff',
                'department' => 'Front Desk',
                'phone'      => '+63 917 000 0006',
                'shift'      => 'evening',
            ],
        ];

        foreach ($users as $i => $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password'      => Hash::make($user['password']),
                    'employee_code' => $user['employee_code'] ?? 'EMP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'hire_date'     => now()->subMonths(rand(1, 24)),
                    'status'        => 'active',
                ])
            );
        }
    }
}