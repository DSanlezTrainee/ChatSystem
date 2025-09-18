<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $invites = Invite::where('invitee_id', $userId)->with('room', 'inviter')->get();

        if (request()->ajax() || request()->wantsJson()) {
            return $invites;
        }

        // Get rooms and users for sidebar
        $user = Auth::user();
        $rooms = $user->rooms;
        $users = \App\Models\User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return Inertia::render('Invites/Index', [
            'invites' => $invites,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'invitee_id' => 'required|exists:users,id',
        ]);
        $data['inviter_id'] = Auth::id();
        $invite = Invite::create($data);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($invite->load('room', 'inviter', 'invitee'), 201);
        }

        return back()->with('success', 'Convite enviado com sucesso.');
    }

    public function update(Request $request, Invite $invite)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,accepted,declined',
        ]);
        $invite->update($data);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($invite->load('room', 'inviter', 'invitee'));
        }

        return back()->with('success', 'Status do convite atualizado.');
    }

    public function manage()
    {
        $sentInvites = Invite::where('inviter_id', Auth::id())
            ->with(['room', 'invitee'])
            ->latest('created_at')
            ->get();

        $receivedInvites = Invite::where('invitee_id', Auth::id())
            ->with(['room', 'inviter'])
            ->latest('created_at')
            ->get();

        // Get rooms and users for sidebar
        $user = Auth::user();
        $rooms = $user->rooms;
        $users = \App\Models\User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return Inertia::render('Invites/Manage', [
            'sentInvites' => $sentInvites,
            'receivedInvites' => $receivedInvites,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user
        ]);
    }
}
