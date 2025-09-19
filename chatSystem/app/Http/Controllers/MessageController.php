<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Server;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{

    /**
     * MessageController constructor.
     * Sets up resource authorization for Message model.
     */
    public function __construct()
    {
        $this->authorizeResource(Message::class, 'message');
    }

    /**
     * Retrieves messages for a room or between users.
     * If room_id is provided, returns messages for that room since the user joined.
     * If to_user_id is provided, returns private messages between the authenticated user and the target user.
     *
     * @param Request $request The HTTP request containing query parameters.
     * @return \Illuminate\Database\Eloquent\Collection The collection of messages.
     */
    public function index(Request $request)
    {
        $roomId = $request->query('room_id');
        $toUserId = $request->query('to_user_id');
        $query = Message::query();

        if ($roomId) {
            $query->where('room_id', $roomId);
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

    /**
     * Stores a new message in the database.
     * Supports room messages and private messages.
     * Returns JSON if requested, otherwise redirects back.
     *
     * @param Request $request The HTTP request containing message data.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
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

        if (!empty($data['room_id'])) {
            return redirect()->back();
        }
        if (!empty($data['to_user_id'])) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Updates the content of an existing message.
     * Validates input and updates the message, then redirects back.
     *
     * @param Request $request The HTTP request containing updated message data.
     * @param Message $message The message to update.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Message $message)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $message->update($data);
        return redirect()->back();
    }

    /**
     * Deletes a message from the database.
     * Redirects back after deletion.
     *
     * @param Request $request The HTTP request.
     * @param Message $message The message to delete.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Message $message)
    {
        $message->delete();
        return redirect()->back();
    }

    /**
     * Shows the direct message page with a specific user.
     * Loads all rooms and users for the sidebar, and retrieves messages between the authenticated user and the target user.
     *
     * @param int $user The target user's ID.
     * @return \Inertia\Response
     */
    public function directMessagePage($user)
    {
        $currentUser = User::find(Auth::id());

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
            'server' => Server::first(),
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $currentUser,
            'toUser' => $toUser,
            'messages' => $messages
        ]);
    }

    /**
     * Handles file upload for chat messages.
     * Validates the file, stores it, and creates a message referencing the file.
     *
     * @param Request $request The HTTP request containing the file and message data.
     * @return \Illuminate\Http\JsonResponse
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

        $message = Message::create([
            'user_id' => $userId,
            'room_id' => $data['room_id'] ?? null,
            'to_user_id' => $data['to_user_id'] ?? null,
            'file_path' => $path,
            'content' => $file->getClientOriginalName(), 
        ]);

        return response()->json($message->load(['user', 'room', 'toUser']), 201);
    }
}
