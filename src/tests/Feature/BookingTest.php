<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->room = Room::create([
            'name'     => 'Test Room',
            'location' => 'Floor 1',
            'capacity' => 8,
        ]);
    }

    public function test_can_create_booking(): void
    {
        $response = $this->postJson('/api/bookings', [
            'user_id'   => 1,
            'room_id'   => $this->room->id,
            'title'     => 'Sprint Planning',
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at'   => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => ['id', 'user_id', 'title', 'room', 'starts_at', 'ends_at'],
                 ]);
    }

    public function test_cannot_double_book_room(): void
    {
        $start = now()->addDay()->format('Y-m-d H:i:s');
        $end   = now()->addDay()->addHour()->format('Y-m-d H:i:s');

        Booking::create([
            'user_id'   => 1,
            'room_id'   => $this->room->id,
            'title'     => 'First booking',
            'starts_at' => $start,
            'ends_at'   => $end,
        ]);

        $response = $this->postJson('/api/bookings', [
            'user_id'   => 2,
            'room_id'   => $this->room->id,
            'title'     => 'Conflicting booking',
            'starts_at' => $start,
            'ends_at'   => $end,
        ]);

        $response->assertStatus(409);
    }

    public function test_can_list_bookings_by_user(): void
    {
        Booking::create([
            'user_id'   => 42,
            'room_id'   => $this->room->id,
            'title'     => 'My meeting',
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at'   => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response = $this->getJson('/api/bookings?user_id=42');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_can_list_bookings_by_room(): void
    {
        Booking::create([
            'user_id'   => 1,
            'room_id'   => $this->room->id,
            'title'     => 'Room meeting',
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at'   => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response = $this->getJson("/api/rooms/{$this->room->id}/bookings");

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_validation_rejects_past_start_date(): void
    {
        $response = $this->postJson('/api/bookings', [
            'user_id'   => 1,
            'room_id'   => $this->room->id,
            'title'     => 'Past meeting',
            'starts_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'ends_at'   => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['starts_at']);
    }
}
