<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BookingService
{
    /**
     * Creates a new booking if the time slot is available for the specified room.
     *
     * @throws \RuntimeException if slot is already booked.
     */
    public function create(array $data): Booking
    {
        return Cache::lock('room_booking_' . $data['room_id'], 10)->block(5, function () use ($data) {

            return DB::transaction(function () use ($data) {
                // Perform the overlap check
                $exists = Booking::where('room_id', $data['room_id'])
                    ->overlapping($data['starts_at'], $data['ends_at'])
                    ->exists();


                if ($exists) {
                    throw new \RuntimeException(
                        'This room is already booked for the selected time slot.'
                    );
                }

                return Booking::create($data);
            });
        });
    }

    /**
     * Lists bookings for a specific user.
     */
    public function listByUser(int $userId): LengthAwarePaginator
    {
        return Booking::with('room')
            ->where('user_id', $userId)
            ->orderBy('starts_at')
            ->paginate(20);
    }

    /**
     * Lists bookings for a specific room.
     */
    public function listByRoom(int $roomId): LengthAwarePaginator
    {
        // Ensure the room exists
        Room::findOrFail($roomId);

        return Booking::with('room')
            ->where('room_id', $roomId)
            ->orderBy('starts_at')
            ->paginate(20);
    }
}
