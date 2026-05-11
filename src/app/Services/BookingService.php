<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
    /**
     * Создать бронирование с проверкой на пересечение времён.
     *
     * @throws \RuntimeException если слот уже занят
     */
    public function create(array $data): Booking
    {
        $conflict = Booking::where('room_id', $data['room_id'])
            ->overlapping($data['starts_at'], $data['ends_at'])
            ->exists();

        if ($conflict) {
            throw new \RuntimeException(
                'This room is already booked for the selected time slot.'
            );
        }

        $booking = Booking::create($data);
        $booking->load('room');

        return $booking;
    }

    /**
     * Список бронирований конкретного пользователя.
     */
    public function listByUser(int $userId): LengthAwarePaginator
    {
        return Booking::with('room')
            ->where('user_id', $userId)
            ->orderBy('starts_at')
            ->paginate(20);
    }

    /**
     * Список бронирований конкретной комнаты.
     */
    public function listByRoom(int $roomId): LengthAwarePaginator
    {
        // Убедимся, что комната существует
        Room::findOrFail($roomId);

        return Booking::with('room')
            ->where('room_id', $roomId)
            ->orderBy('starts_at')
            ->paginate(20);
    }
}
