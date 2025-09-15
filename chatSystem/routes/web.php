<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\RoomController;


Route::middleware(['auth', 'is_admin'])->group(function () {


    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');


    // Invites
    Route::get('invites', [App\Http\Controllers\InviteController::class, 'index']);
    Route::post('invites', [App\Http\Controllers\InviteController::class, 'store']);
    Route::patch('invites/{invite}', [App\Http\Controllers\InviteController::class, 'update']);
});

Route::middleware(['auth'])->group(function () {

    // Messages
    Route::get('messages', [App\Http\Controllers\MessageController::class, 'index']);
    Route::post('messages', [App\Http\Controllers\MessageController::class, 'store']);
    Route::put('messages/{message}', [App\Http\Controllers\MessageController::class, 'update']);
    Route::delete('messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy']);
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
