<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        return Room::with('users')->get();
    }

    public function store(Request $request)
    {
        $roomAdminId = Auth::id();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|string',
            'users' => 'array',
            'users.*' => 'exists:users,id',
        ]);
        $data['owner_id'] = $roomAdminId; // Define o admin como owner

        $room = Room::create($data);
        $users = $data['users'] ?? [];
        if (!in_array($roomAdminId, $users)) {
            $users[] = $roomAdminId;
        }
        $room->users()->sync($users);

        return response()->json($room->load('users'), 201);
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
