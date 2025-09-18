<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Carrega as salas disponíveis para o usuário baseado em sua permissão
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function loadRoomsForUser(User $user)
    {
        if ($user->isAdmin()) {
            return Room::with('users')->get();
        } else {
            return $user->rooms()->with('users')->get();
        }
    }

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

    public function create()
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificação de permissão - apenas admin pode criar salas
        if (!$user->isAdmin()) {
            return redirect()->route('chat')->with('error', 'Você não tem permissão para criar salas');
        }

        // Carrega as salas com base na permissão do usuário
        $rooms = $this->loadRoomsForUser($user);

        // Carrega todos os usuários para seleção
        $users = User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return \Inertia\Inertia::render('Rooms/Create', [
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user
        ]);
    }

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
            'room' => $room,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user,
        ]);
    }
    public function editUsers(Room $room)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificação de permissão - apenas admin pode editar usuários de salas
        if (!$user->isAdmin()) {
            return redirect()->route('chat')->with('error', 'Você não tem permissão para editar usuários de salas');
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
            // Buscar usuários já presentes na sala
            $existingUserIds = $room->users()->pluck('user_id')->toArray();
            $syncData = [];
            $now = now();
            foreach ($data['users'] as $userId) {
                // Se já estava na sala, mantém o joined_at
                $pivot = $room->users()->where('user_id', $userId)->first();
                $joinedAt = $pivot ? $pivot->pivot->joined_at : $now;
                $syncData[$userId] = ['joined_at' => $joinedAt];
            }
            $room->users()->sync($syncData);
        }

        return redirect()->route('rooms.show', $room);
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('chat');
    }
}
