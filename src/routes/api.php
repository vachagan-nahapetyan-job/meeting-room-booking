<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Meeting Room Booking API
|--------------------------------------------------------------------------
|
|  All routes are public — user_id is passed in request body.
*/

// Rooms
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);
Route::get('/rooms/{room}/bookings', [BookingController::class, 'byRoom']);

// Bookings
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings', [BookingController::class, 'byUser']);

// //Swagger
Route::get('/documentation', function () {
    return view('l5-swagger::index');
});
