<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $roomId = $request->query('room_id');
        $toUserId = $request->query('to_user_id');
        $query = Message::query();
        if ($roomId) {
            $query->where('room_id', $roomId);
        }
        if ($toUserId) {
            $query->where('to_user_id', $toUserId)->orWhere('user_id', $toUserId);
        }
        return $query->with(['user', 'room', 'toUser'])->orderBy('created_at')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
            'to_user_id' => 'nullable|exists:users,id',
        ]);
        $data['user_id'] = Auth::id();
        $message = Message::create($data);
        return response()->json($message->load(['user', 'room', 'toUser']), 201);
    }

    public function update(Request $request, Message $message)
    {
        $user = $request->user();
        if ($user->permission !== 'admin' && $user->id !== $message->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = $request->validate([
            'content' => 'required|string',
        ]);
        $message->update($data);
        return response()->json($message->fresh()->load(['user', 'room', 'toUser']));
    }

    public function destroy(Request $request, Message $message)
    {
        $user = $request->user();
        if ($user->permission !== 'admin' && $user->id !== $message->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $message->delete();
        return response()->noContent();
    }
}
