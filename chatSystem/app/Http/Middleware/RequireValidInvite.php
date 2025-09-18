<?php

namespace App\Http\Middleware;

use App\Models\ServerInvite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireValidInvite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não temos um invite_token, não permitir registro
        if (!$request->has('invite_token') && !$request->session()->has('invite_token')) {
            return abort(410, 'This invite link has expired');
        }

        // Pegar o token do request ou da sessão
        $token = $request->input('invite_token') ?? $request->session()->get('invite_token');

        // Verificar se o token existe e está pendente
        $invite = ServerInvite::where('token', $token)->where('status', 'pending')->first();

        if (!$invite) {
            return abort(410, 'This invite link has expired');
        }

        // Guardar o token na sessão para uso posterior
        $request->session()->put('invite_token', $token);

        return $next($request);
    }
}
