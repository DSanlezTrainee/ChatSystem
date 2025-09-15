<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;


Route::middleware('auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Messages
    Route::get('messages', [App\Http\Controllers\MessageController::class, 'index']);
    Route::post('messages', [App\Http\Controllers\MessageController::class, 'store']);

    // Invites
    Route::get('invites', [App\Http\Controllers\InviteController::class, 'index']);
    Route::post('invites', [App\Http\Controllers\InviteController::class, 'store']);
    Route::patch('invites/{invite}', [App\Http\Controllers\InviteController::class, 'update']);
});
