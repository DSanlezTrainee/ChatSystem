<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\RegisteredUserController as FortifyRegisteredUserController;

class CustomRegistrationController extends Controller
{
    /**
     * Mostrar página de registro apenas se tiver convite válido
     */
    public function create(Request $request, FortifyRegisteredUserController $controller)
    {
        // Verificação já feita pelo middleware RequireValidInvite
        return $controller->create($request);
    }

    /**
     * Registrar um novo usuário
     */
    public function store(Request $request, FortifyRegisteredUserController $controller)
    {
        // Injeção de dependências
        return app()->call([$controller, 'store'], ['request' => $request]);
    }
}
