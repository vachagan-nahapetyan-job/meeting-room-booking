<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['service' => 'meeting-room-booking', 'version' => '1.0']);
});
