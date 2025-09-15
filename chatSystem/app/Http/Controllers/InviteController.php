<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        return Invite::where('invitee_id', $userId)->with('room', 'inviter')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'invitee_id' => 'required|exists:users,id',
        ]);
        $data['inviter_id'] = Auth::id();
        $invite = Invite::create($data);
        return response()->json($invite->load('room', 'inviter', 'invitee'), 201);
    }

    public function update(Request $request, Invite $invite)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,accepted,declined',
        ]);
        $invite->update($data);
        return response()->json($invite->load('room', 'inviter', 'invitee'));
    }
}
