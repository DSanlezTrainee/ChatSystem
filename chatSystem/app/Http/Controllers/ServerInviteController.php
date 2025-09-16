<?php

namespace App\Http\Controllers;

use App\Models\ServerInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ServerInviteController extends Controller
{
    // Retorna o convite atual (pending) do admin logado
    public function current()
    {
        $invite = ServerInvite::where('inviter_id', Auth::id())
            ->where('status', 'pending')
            ->latest('created_at')
            ->first();
        return response()->json([
            'invite_link' => $invite ? url('/invite/' . $invite->token) : null,
            'token' => $invite ? $invite->token : null,
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
        return response()->json([
            'invite_link' => url('/invite/' . $invite->token),
            'token' => $invite->token,
        ]);
    }

    // Usa/aceita um convite geral
    public function accept($token)
    {
        $invite = ServerInvite::where('token', $token)->where('status', 'pending')->first();
        if (!$invite) {
            return redirect('/register')->with('error', 'Convite inválido ou expirado.');
        }
        return redirect('/register?invite_token=' . $token);
    }
}
