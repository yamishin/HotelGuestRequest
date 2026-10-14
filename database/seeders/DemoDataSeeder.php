<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Notification;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Models\Stay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Create 6 demo guests ---
        $guestsData = [
            ['John', 'Smith', 'john.smith@email.com', '+63 917 123 4567'],
            ['David', 'Chen', 'd.chen@email.com', '+63 919 345 6789'],
            ['Robert', 'Lim', 'r.lim@email.com', '+63 921 567 8901'],
            ['Maria', 'Santos', 'maria.s@email.com', '+63 918 234 5678'],
            ['Anna', 'Reyes', 'anna.reyes@email.com', '+63 920 456 7890'],
            ['Michael', 'Tan', 'm.tan@email.com', '+63 922 678 9012'],
        ];

        $guests = [];
        foreach ($guestsData as $i => $g) {
            $guests[] = Guest::create([
                'guest_code' => 'G-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'first_name' => $g[0],
                'last_name' => $g[1],
                'email' => $g[2],
                'phone' => $g[3],
            ]);
        }

        // --- 2. Create ACTIVE stays for first 3 guests ---
        $activeRooms = ['505', '1203', '1508'];
        foreach (array_slice($guests, 0, 3) as $i => $guest) {
            $room = Room::where('room_number', $activeRooms[$i])->first();
            if (!$room) continue;

            $stay = Stay::create([
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => now()->subDays(2),
                'check_out_date' => now()->addDays(3),
                'actual_check_in' => now()->subDays(2),
                'status' => 'active',
                'access_token' => Str::random(32),
                'num_guests' => 1,
            ]);

            $room->update(['status' => 'occupied']);

            // Create 2-3 service requests per active guest
            $requests = [
                ['Extra Towels', 'Housekeeping', 'completed'],
                ['Wi-Fi not working', 'IT', 'in_progress'],
                ['Aircon not cooling', 'Maintenance', 'pending'],
                ['Extra pillows requested', 'Housekeeping', 'assigned'],
            ];

            foreach (array_slice($requests, 0, rand(2, 3)) as $j => $req) {
                // Find a staff member in that department
                $assignee = \App\Models\User::where('role', 'staff')
                    ->where('department', $req[1])
                    ->first();

                ServiceRequest::create([
                    'request_code' => 'REQ-' . str_pad(($i * 3) + $j + 1, 4, '0', STR_PAD_LEFT),
                    'stay_id'      => $stay->id,
                    'title'        => $req[0],
                    'description'  => 'Guest requested ' . strtolower($req[0]),
                    'department'   => $req[1],
                    'status'       => $req[2],
                    'assigned_to'  => ($req[2] === 'pending') ? null : $assignee?->id,
                    'completed_at' => ($req[2] === 'completed') ? now() : null,
                ]);
            }
        }

        // --- 3. Create COMPLETED stays for guests 4 & 5 ---
        foreach (array_slice($guests, 3, 2) as $i => $guest) {
            $room = Room::where('room_number', $i === 0 ? '702' : '908')->first();
            if (!$room) continue;

            Stay::create([
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => now()->subDays(20),
                'check_out_date' => now()->subDays(15),
                'actual_check_in' => now()->subDays(20),
                'actual_check_out' => now()->subDays(15),
                'status' => 'completed',
                'num_guests' => 1,
            ]);
        }

        // --- 4. Create UPCOMING stay for guest 6 ---
        $room = Room::where('room_number', '1005')->first();
        if ($room && isset($guests[5])) {
            Stay::create([
                'guest_id' => $guests[5]->id,
                'room_id' => $room->id,
                'check_in_date' => now()->addDays(7),
                'check_out_date' => now()->addDays(12),
                'status' => 'upcoming',
                'num_guests' => 2,
            ]);
        }

        // --- 5. Create notifications ---
        $notifs = [
            ['request', 'New Request', 'Room 1203 requested room service', 'frontdesk'],
            ['check_in', 'Check-in', 'John Smith has checked in to Room 505', 'frontdesk'],
            ['request', 'Completed', 'Room 803 towels request marked done', 'frontdesk'],
            ['review', 'Review', 'Room 1001 left a 5-star review', 'frontdesk'],
            ['payment', 'Payment', 'Room 702 checked out and settled balance', 'frontdesk'],
        ];
        foreach ($notifs as $n) {
            Notification::create([
                'type' => $n[0],
                'title' => $n[1],
                'message' => $n[2],
                'target_role' => $n[3],
                'is_read' => false,
            ]);
        }
    }
}