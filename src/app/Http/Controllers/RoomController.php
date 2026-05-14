<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/rooms",
     *     summary="List all meeting rooms",
     *     tags={"Rooms"},
     *     @OA\Response(
     *         response=200,
     *         description="List of rooms",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Room")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        return RoomResource::collection(Room::all());
    }

    /**
     * @OA\Get(
     *     path="/rooms/{id}",
     *     summary="Get room details",
     *     tags={"Rooms"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Room")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Room not found")
     * )
     */
    public function show(Room $room): RoomResource
    {
        return new RoomResource($room);
    }
}