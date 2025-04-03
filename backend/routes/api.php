<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get(	'/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::get(	'/available-rooms', [BookingController::class, 'availableRooms']);
Route::get(	'/available-rooms-v2', [BookingController::class, 'availableRoomsV2']);

Route::get(	'/rooms', [RoomController::class, 'index']);