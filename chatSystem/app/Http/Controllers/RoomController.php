<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Loads the available rooms for the user based on their permission
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    /**
     * Loads all rooms available to the given user.
     * Admins see all rooms, regular users see only rooms they belong to.
     *
     * @param User $user The user whose rooms should be loaded.
     * @return \Illuminate\Database\Eloquent\Collection The collection of rooms.
     */
    private function loadRoomsForUser(User $user)
    {
        if ($user->isAdmin()) {
            return Room::with('users')->get();
        } else {
            return $user->rooms()->with('users')->get();
        }
    }

    /**
     * Displays the list of rooms available to the authenticated user.
     * Returns a JSON error if the user is not authenticated.
     *
     * @return \Inertia\InertiaResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $rooms = $this->loadRoomsForUser($user);

        return \Inertia\Inertia::render('Rooms/Index', [
            'rooms' => $rooms
        ]);
    }

    /**
     * Shows the room creation page for admins.
     * Redirects to login if not authenticated, or to chat if not admin.
     * Loads all rooms and users for selection.
     *
     * @return \Inertia\InertiaResponse|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login');
        }

        // Permission check - only admin can create rooms
        if (!$user->isAdmin()) {
            return redirect()->route('chat')->with('error', 'You do not have permission to create rooms');
        }

        // Loads rooms based on user permission
        $rooms = $this->loadRoomsForUser($user);

        // Loads all users for selection
        $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return \Inertia\Inertia::render('Rooms/Create', [
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user
        ]);
    }

    /**
     * Handles the creation of a new room.
     * Validates input, creates the room, and syncs users (including the admin).
     * Redirects to the new room on success, or back with errors on failure.
     *
     * @param Request $request The HTTP request containing room data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $roomAdminId = Auth::id();
            if (!$roomAdminId) {
                return redirect()->back()->with('error', 'User not authenticated.');
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
            $syncData = [];
            $now = now();
            foreach ($users as $userId) {
                $syncData[$userId] = ['joined_at' => $now];
            }
            $room->users()->sync($syncData);

            return redirect()->route('rooms.show', $room);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Shows the details of a specific room, including its users and messages.
     * Loads all rooms and users for the sidebar.
     * Returns a JSON error if the user is not authenticated.
     *
     * @param Room $room The room to display.
     * @return \Inertia\InertiaResponse|\Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $joinedAt = $room->users()->where('user_id', $user->id)->first()?->pivot->joined_at;

        $room->load([
            'users' => function ($query) {
                $query->withPivot('joined_at');
            },
            'messages' => function ($query) use ($joinedAt) {
                if ($joinedAt) {
                    $query->where('created_at', '>=', $joinedAt);
                }
                $query->orderBy('created_at', 'asc');
            }
        ]);

        // Get all rooms for the sidebar using the shared method
        $rooms = $this->loadRoomsForUser($user);

        // Get all users for the sidebar
        $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return \Inertia\Inertia::render('Rooms/Show', [
            'server' => Server::first(),
            'room' => $room,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user,
        ]);
    }
    /**
     * Shows the edit users page for a room. Only admins can edit room users.
     * Loads the room with its users, all rooms for the sidebar, and all users for selection.
     *
     * @param Room $room
     * @return \Inertia\InertiaResponse|\Illuminate\Http\RedirectResponse
     */
    /**
     * Shows the edit users page for a room. Only admins can edit room users.
     * Loads the room with its users, all rooms for the sidebar, and all users for selection.
     * Redirects to login if not authenticated, or to chat if not admin.
     *
     * @param Room $room The room to edit users for.
     * @return \Inertia\InertiaResponse|\Illuminate\Http\RedirectResponse
     */
    public function editUsers(Room $room)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login');
        }

        // Permission check - only admin can edit room users
        if (!$user->isAdmin()) {
            return redirect()->route('chat')->with('error', 'You do not have permission to edit room users');
        }

        // Loads the room with its users
        $room->load('users');

        // Loads rooms based on user permission
        $rooms = $this->loadRoomsForUser($user);

        // Loads all users for selection
        $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return \Inertia\Inertia::render('Rooms/EditUsers', [
            'room' => $room,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user,
            'selectedUserIds' => $room->users->pluck('id')->toArray()
        ]);
    }

    /**
     * Updates the room's details and its user list.
     * Validates input, updates the room, and syncs users while preserving join dates.
     * Redirects to the room's page after update.
     *
     * @param Request $request The HTTP request containing updated room data.
     * @param Room $room The room to update.
     * @return \Illuminate\Http\RedirectResponse
     */
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
            // Get users already present in the room
            $existingUserIds = $room->users()->pluck('user_id')->toArray();
            $syncData = [];
            $now = now();
            foreach ($data['users'] as $userId) {
                // If the user was already in the room, keep the joined_at value
                $pivot = $room->users()->where('user_id', $userId)->first();
                $joinedAt = $pivot ? $pivot->pivot->joined_at : $now;
                $syncData[$userId] = ['joined_at' => $joinedAt];
            }
            $room->users()->sync($syncData);
        }

        return redirect()->route('rooms.show', $room);
    }

    /**
     * Deletes the specified room from the database.
     * Redirects to the default room (ID 1) after deletion.
     *
     * @param Room $room The room to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.show', 1);
    }
}
