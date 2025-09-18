<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ServerInviteController;
use App\Http\Controllers\CustomRegistrationController;

//Invites
Route::get('/invite/{token}', [ServerInviteController::class, 'accept']);

Route::get('/register', [CustomRegistrationController::class, 'create'])
    ->middleware(['guest', 'require_valid_invite'])
    ->name('register');

Route::post('/register', [CustomRegistrationController::class, 'store'])
    ->middleware(['guest', 'require_valid_invite']);

Route::middleware(['auth', 'is_admin'])->group(function () {

    // Admin-only routes
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::get('/rooms/{room}/users', [RoomController::class, 'editUsers'])->name('rooms.users');

    // Invites Server
    Route::post('/server-invite/generate', [ServerInviteController::class, 'generate']);
    Route::get('/server-invite/current', [ServerInviteController::class, 'current'])->middleware('auth');
    Route::get('/server-invite/manage', [ServerInviteController::class, 'manage'])->name('server-invite.manage');

    // Invites Rooms
    Route::get('invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('invites', [InviteController::class, 'store'])->name('invites.store');
    Route::patch('invites/{invite}', [InviteController::class, 'update'])->name('invites.update');
    Route::get('invites/manage', [InviteController::class, 'manage'])->name('invites.manage');

    //Server Management
    Route::put('/servers/{server}', [ServerController::class, 'update']);

    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

Route::middleware(['auth'])->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rooms routes 
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/chat/users/{user}', [MessageController::class, 'directMessagePage'])->name('chat.dm');

    // Messages
    Route::get('messages', [MessageController::class, 'index']);
    Route::post('messages', [MessageController::class, 'store']);
    Route::put('messages/{message}', [MessageController::class, 'update']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);

    // Upload de ficheiros para o chat
    Route::post('messages/upload', [MessageController::class, 'uploadFile'])->name('messages.upload');

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
