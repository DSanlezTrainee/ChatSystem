<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }
        $rooms = $user->rooms()->with('users')->get();
        return $rooms;
    }

    public function store(Request $request)
    {
        try {
            $roomAdminId = Auth::id();
            if (!$roomAdminId) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|string',
                'users' => 'array',
                'users.*' => 'exists:users,id',
            ]);
            $data['owner_id'] = $roomAdminId;

            $room = Room::create($data);
            $users = $data['users'] ?? [];
            if (!in_array($roomAdminId, $users)) {
                $users[] = $roomAdminId;
            }
            $room->users()->sync($users);

            return response()->json($room->load('users'), 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function show(Room $room)
    {
        return $room->load('users', 'messages');
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'nullable|string',
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ]);
        $room->update($data);
        if (isset($data['users'])) {
            $room->users()->sync($data['users']);
        }
        return response()->json($room->load('users'));
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return response()->noContent();
    }
}
