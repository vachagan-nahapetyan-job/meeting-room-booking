<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    /**
     * @OA\Post(
     *     path="/bookings",
     *     summary="Create a new booking",
     *     description="Creates a new meeting room booking",
     *     operationId="createBooking",
     *     tags={"Bookings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "room_id", "title", "starts_at", "ends_at"},
     *             @OA\Property(property="user_id", type="integer", example=42, description="User ID (no auth)"),
     *             @OA\Property(property="room_id", type="integer", example=1, description="Room ID"),
     *             @OA\Property(property="title", type="string", example="Sprint Planning", maxLength=255),
     *             @OA\Property(property="starts_at", type="string", format="datetime", example="2026-12-10 10:00:00"),
     *             @OA\Property(property="ends_at", type="string", format="datetime", example="2026-12-10 11:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Booking")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Time slot conflict",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This room is already booked for the selected time slot.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->create($request->validated());
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/bookings",
     *     summary="Get user bookings",
     *     description="Returns paginated bookings for a specific user",
     *     operationId="getUserBookings",
     *     tags={"Bookings"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=42)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Booking")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function byUser(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        $paginator = $this->bookingService->listByUser((int) $request->user_id);

        return BookingResource::collection($paginator);
    }

        /**
        * @OA\Get(
        *     path="/rooms/{room}/bookings",
        *     summary="Get bookings for a room",
        *     description="Returns paginated bookings for a specific room",
        *     operationId="getRoomBookings",
        *     tags={"Rooms", "Bookings"},
        *     @OA\Parameter(
        *         name="room",
        *         in="path",
        *         description="Room ID",
        *         required=true,
        *         @OA\Schema(type="integer", example=1)
        *     ),
        *     @OA\Parameter(
        *         name="page",
        *         in="query",
        *         description="Page number",
        *         required=false,
        *         @OA\Schema(type="integer", example=1)
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Successful operation",
        *         @OA\JsonContent(
        *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Booking")),
        *             @OA\Property(property="links", type="object"),
        *             @OA\Property(property="meta", type="object")
        *         )
        *     ),
        *     @OA\Response(
        *         response=404,
        *         description="Room not found",
        *         @OA\JsonContent(
        *             @OA\Property(property="message", type="string", example="Resource not found.")
        *         )
        *     )
        * )
        */
    public function byRoom(int $roomId): AnonymousResourceCollection
    {
        $paginator = $this->bookingService->listByRoom($roomId);

        return BookingResource::collection($paginator);
    }
}
