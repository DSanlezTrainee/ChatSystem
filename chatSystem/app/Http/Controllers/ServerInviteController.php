<?php

namespace App\Http\Controllers;

use App\Models\ServerInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ServerInviteController extends Controller
{
    // Retorna o convite atual (pending) do admin logado
    public function current()
    {
        $invite = ServerInvite::where('inviter_id', Auth::id())
            ->where('status', 'pending')
            ->latest('created_at')
            ->first();

        // For AJAX requests, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'invite_link' => $invite ? url('/invite/' . $invite->token) : null,
                'token' => $invite ? $invite->token : null,
            ]);
        }

        // For normal requests, return Inertia view
        // Get rooms and users for sidebar
        $user = Auth::user();
        $rooms = $user->rooms;
        $users = \App\Models\User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

        return Inertia::render('ServerInvite/Show', [
            'invite_link' => $invite ? url('/invite/' . $invite->token) : null,
            'token' => $invite ? $invite->token : null,
            'rooms' => $rooms,
            'users' => $users,
            'currentUser' => $user
        ]);
    }

    // Gera um novo convite geral
    public function generate()
    {
        // Invalida todos os convites anteriores do admin
        ServerInvite::where('inviter_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        $token = Str::uuid()->toString();
        $invite = ServerInvite::create([
            'token' => $token,
            'inviter_id' => Auth::id(),
            'status' => 'pending',
        ]);

        // Para requisições AJAX/JSON, retorna JSON
        if (request()->ajax() || request()->wantsJson() || request()->header('X-Inertia') === 'false') {
            return response()->json([
                'invite_link' => url('/invite/' . $invite->token),
                'token' => $invite->token,
            ]);
        }

        // Para requisições Inertia normais, redireciona para a página de gerenciamento
        return redirect()->route('server-invite.manage')->with('success', 'New invite link generated successfully.');
    }

    // Usa/aceita um convite geral
    public function accept($token)
    {
        $invite = ServerInvite::where('token', $token)->first();

        // Se o convite não existir, retornar 404
        if (!$invite) {
            abort(404, 'Invite not found');
        }

        // Se o status for expirado, retornar erro 410 (Gone)
        if ($invite->status === 'expired') {
            abort(410, 'This invite link has expired');
        }

        // Se o status não for pending, retornar erro 403
        if ($invite->status !== 'pending') {
            abort(403, 'This invite link is not valid');
        }

        return redirect('/register?invite_token=' . $token);
    }

    // Página para gerenciar convites
    public function manage()
    {
        try {
            $pendingInvite = ServerInvite::where('inviter_id', Auth::id())
                ->where('status', 'pending')
                ->latest('created_at')
                ->first();

            $usedInvites = ServerInvite::where('inviter_id', Auth::id())
                ->where('status', 'used')
                ->with('invitee')
                ->latest('updated_at')
                ->take(10)
                ->get();

            // Get rooms and users for sidebar
            $user = Auth::user();
            $rooms = $user->rooms;
            $users = \App\Models\User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

            return Inertia::render('ServerInvite/Manage', [
                'pendingInvite' => $pendingInvite ? [
                    'token' => $pendingInvite->token,
                    'invite_link' => url('/invite/' . $pendingInvite->token),
                    'created_at' => $pendingInvite->created_at,
                ] : null,
                'usedInvites' => $usedInvites,
                'rooms' => $rooms,
                'users' => $users,
                'currentUser' => $user
            ]);
        } catch (\Exception $e) {
            // Log o erro
            Log::error('Error loading the pages with invites: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('rooms.index')->with('error', 'An error occurred while accessing the invites page.');
        }
    }
}
