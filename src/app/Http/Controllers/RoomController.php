<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    /**
     * GET /api/rooms
     * Список всех переговорок.
     */
    public function index(): AnonymousResourceCollection
    {
        return RoomResource::collection(Room::all());
    }

    /**
     * GET /api/rooms/{room}
     * Детали одной переговорки.
     */
    public function show(Room $room): RoomResource
    {
        return new RoomResource($room);
    }
}
