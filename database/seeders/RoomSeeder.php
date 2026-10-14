<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Room numbers to skip on each floor
        $roomNumbers = [501, 502, 503, 505, 506, 507, 508, 509, 510, 511, 512, 513, 515, 516, 517, 518, 519];
        $suffixes = array_map(fn($num) => $num % 100, $roomNumbers); // [1, 2, 3, 5, ..., 19]

        $roomTypes = ['Standard', 'Deluxe', 'Suite', 'Executive'];

        for ($floor = 5; $floor <= 16; $floor++) {
            foreach ($suffixes as $suffix) {
                $roomNumber = ($floor * 100) + $suffix;

                // Assign a room type based on floor
                if ($floor >= 14) {
                    $type = 'Suite';
                } elseif ($floor >= 11) {
                    $type = 'Executive';
                } elseif ($floor >= 8) {
                    $type = 'Deluxe';
                } else {
                    $type = 'Standard';
                }

                Room::create([
                    'room_number' => (string) $roomNumber,
                    'floor' => $floor,
                    'type' => $type,
                    'status' => 'available',
                ]);
            }
        }
    }
}