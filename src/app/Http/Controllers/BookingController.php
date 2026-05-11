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
     * POST /api/bookings
     * Создать бронирование.
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
     * GET /api/bookings?user_id=42
     * Список бронирований текущего пользователя.
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
     * GET /api/rooms/{room}/bookings
     * Список бронирований по комнате.
     */
    public function byRoom(int $roomId): AnonymousResourceCollection
    {
        $paginator = $this->bookingService->listByRoom($roomId);

        return BookingResource::collection($paginator);
    }
}
