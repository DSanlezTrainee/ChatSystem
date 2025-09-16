<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServerInviteController;

//Invites
Route::get('/invite/{token}', [ServerInviteController::class, 'accept']);

Route::middleware(['auth', 'is_admin'])->group(function () {

    // Rooms

    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Invites Server
    Route::post('/server-invite/generate', [ServerInviteController::class, 'generate']);
    Route::get('/server-invite/current', [ServerInviteController::class, 'current'])->middleware('auth');

    // Invites Rooms
    Route::get('invites', [InviteController::class, 'index']);
    Route::post('invites', [InviteController::class, 'store']);
    Route::patch('invites/{invite}', [InviteController::class, 'update']);
});

Route::middleware(['auth'])->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    
    Route::get('/chat', function () {
        return Inertia::render('Chat/ChatPage');
    })->name('chat');


    // Messages
    Route::get('messages', [MessageController::class, 'index']);
    Route::post('messages', [MessageController::class, 'store']);
    Route::get('messages/{room}', [MessageController::class, 'getMessagesByRoom']);
    Route::put('messages/{message}', [MessageController::class, 'update']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);

    // Users for DMs
    Route::get('/users', function () {
        return \App\Models\User::all(['id', 'name', 'avatar', 'status']);
    });
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
