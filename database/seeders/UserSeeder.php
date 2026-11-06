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
            // 1. Front Desk Supervisor (Handles routing + Concierge)
            [
                'name' => 'Jane FrontDesk Sup',
                'email' => 'supervisor.frontdesk@blulane.com',
                'password' => 'password',
                'role' => 'supervisor',
                'department' => 'Front Desk',
                'phone' => '+63 917 000 0001',
                'shift' => 'morning',
            ],
            // 2. Housekeeping Supervisor
            [
                'name' => 'Mark Housekeeping Sup',
                'email' => 'supervisor.housekeeping@blulane.com',
                'password' => 'password',
                'role' => 'supervisor',
                'department' => 'Housekeeping',
                'phone' => '+63 917 000 0002',
                'shift' => 'morning',
            ],
            // 3. IT Supervisor
            [
                'name' => 'Sarah IT Sup',
                'email' => 'supervisor.it@blulane.com',
                'password' => 'password',
                'role' => 'supervisor',
                'department' => 'IT',
                'phone' => '+63 917 000 0007',
                'shift' => 'morning',
            ],
            // 4. Maintenance Supervisor
            [
                'name' => 'Victor Maintenance Sup',
                'email' => 'supervisor.maintenance@blulane.com',
                'password' => 'password',
                'role' => 'supervisor',
                'department' => 'Maintenance',
                'phone' => '+63 917 000 0008',
                'shift' => 'morning',
            ],
            // 5. Staff (Housekeeping)
            [
                'name' => 'Maria Housekeeping',
                'email' => 'maria.housekeeping@blulane.com',
                'password' => 'password',
                'role' => 'staff',
                'department' => 'Housekeeping',
                'phone' => '+63 917 000 0003',
                'shift' => 'morning',
            ],
            // 6. Staff (IT)
            [
                'name' => 'Sam IT',
                'email' => 'sam.it@blulane.com',
                'password' => 'password',
                'role' => 'staff',
                'department' => 'IT',
                'phone' => '+63 917 000 0005',
                'shift' => 'morning',
            ],

            // 0. Administrator (General Manager)
            [
                'name' => 'Admin User',
                'email' => 'admin@blulane.com',
                'password' => 'password',
                'role' => 'admin',
                'department' => 'Management',
                'phone' => '+63 917 000 0000',
                'shift' => 'morning',
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
