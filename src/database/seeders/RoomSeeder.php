<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if rooms already exist
        if (DB::table('rooms')->count() > 0) {
            return;
        }

        $rooms = [
            ['name' => 'Alpha', 'location' => 'Floor 1, Room 101', 'capacity' => 6],
            ['name' => 'Beta',  'location' => 'Floor 1, Room 102', 'capacity' => 10],
            ['name' => 'Gamma', 'location' => 'Floor 2, Room 201', 'capacity' => 4],
            ['name' => 'Delta', 'location' => 'Floor 3, Room 301', 'capacity' => 20],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert(array_merge($room, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}