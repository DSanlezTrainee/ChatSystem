<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;

// Test route without authentication
/*Route::get('/test-auth', function () {
    return response()->json(['message' => 'API is working without authentication'], 200);
});

// Test route for debug
Route::get('/debug-rooms', function () {
    // Return all rooms without authentication
    return \App\Models\Room::with('users')->get();
});

// Test route that ignores CSRF and only checks authenticated user
Route::get('/auth-simple', function () {
    try {
        // Acessa diretamente o Auth facade sem usar a sessão
        return [
            'authenticated' => \Illuminate\Support\Facades\Auth::check(),
            'user_id' => \Illuminate\Support\Facades\Auth::id()
        ];
    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage()
        ];
    }
});

// API Login test endpoint
Route::post('/api-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = \Illuminate\Support\Facades\Auth::user();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'session_id' => $request->session()->getId()
        ]);
    }

    return response()->json([
        'message' => 'The provided credentials do not match our records.',
    ], 401);
});

// Endpoint to test user and session
Route::get('/session-info', function (Request $request) {
    return [
        'has_session' => $request->hasSession(),
        'session_status' => session_status(),
        'session_id' => $request->session()->getId(),
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'cookies' => $request->cookies->all()
    ];
});

// Route to test user authentication status
Route::get('/auth-status', function (Request $request) {
    try {
        return response()->json([
            'authenticated' => $request->user() ? true : false,
            'user' => $request->user(),
            'has_session' => $request->hasSession()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'authenticated' => false,
            'error' => $e->getMessage(),
            'has_session' => $request->hasSession()
        ]);
    }
});*/

// Todas as rotas autenticadas foram movidas para web.php
