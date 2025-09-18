<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Message::class, 'message');
    }

    public function index(Request $request)
    {
        $roomId = $request->query('room_id');
        $toUserId = $request->query('to_user_id');
        $query = Message::query();

        if ($roomId) {
            // Room messages
            $query->where('room_id', $roomId);
            // Filtrar por joined_at do usuário na sala
            $currentUserId = Auth::id();
            $room = Room::find($roomId);
            if ($room) {
                $pivot = $room->users()->where('user_id', $currentUserId)->first();
                $joinedAt = $pivot ? $pivot->pivot->joined_at : null;
                if ($joinedAt) {
                    $query->where('created_at', '>=', $joinedAt);
                }
            }
        } elseif ($toUserId) {
            // Private messages between users
            $currentUserId = Auth::id();
            $query->where(function ($q) use ($toUserId, $currentUserId) {
                $q->where('user_id', $currentUserId)->where('to_user_id', $toUserId)
                    ->orWhere('user_id', $toUserId)->where('to_user_id', $currentUserId);
            })->whereNull('room_id');
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

        if ($request->wantsJson()) {
            return response()->json($message->load(['user', 'room', 'toUser']), 201);
        }

        // If this was a room message, redirect back to the room
        if ($data['room_id']) {
            return redirect()->back();
        }

        // If this was a direct message, redirect back to the chat with that user
        if ($data['to_user_id']) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function update(Request $request, Message $message)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $message->update($data);
        return redirect()->back();
    }

    public function destroy(Request $request, Message $message)
    {
        $message->delete();
        return redirect()->back();
    }

    /**
     * Show the direct message page with a specific user
     *
     * @param int $user User ID
     * @return \Inertia\Response
     */
    public function directMessagePage($user)
    {
        // Obter o usuário atual
        $currentUser = User::find(Auth::id());

        // Obter o usuário de destino
        $toUser = User::findOrFail($user);


        if ($currentUser->permission === 'admin') {
            $rooms = Room::with('users')->get();
        } else {
            $rooms = $currentUser->rooms()->with('users')->get();
        }

        $users = User::where('id', '!=', $currentUser->id)
            ->get(['id', 'name', 'avatar', 'status']);

        $request = new Request();
        $request->merge(['to_user_id' => $toUser->id]);
        $messages = $this->index($request);

        return \Inertia\Inertia::render('Chat/DirectMessage', [
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $currentUser,
            'toUser' => $toUser,
            'messages' => $messages
        ]);
    }

    /**
     * Recebe upload de ficheiro para o chat
     */
    public function uploadFile(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|file|max:5120', // 5MB
            'room_id' => 'nullable|exists:rooms,id',
            'to_user_id' => 'nullable|exists:users,id',
        ]);
        $userId = Auth::id();
        $file = $request->file('file');
        $path = $file->store('chat_files', 'public');

        // Criar mensagem com referência ao ficheiro
        $message = Message::create([
            'user_id' => $userId,
            'room_id' => $data['room_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'file_path' => $path,
            'content' => $file->getClientOriginalName(), // opcional: pode ser vazio ou nome do ficheiro
        ]);

        return response()->json($message->load(['user', 'room', 'toUser']), 201);
    }
}
