<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\RegisteredUserController as FortifyRegisteredUserController;

class CustomRegistrationController extends Controller
{
    /**
     * Show a registration page only if there is a valid invite
     */
    public function create(Request $request, FortifyRegisteredUserController $controller)
    {
        // Verificação já feita pelo middleware RequireValidInvite
        return $controller->create($request);
    }

    /**
     * Register a new user
     */
    public function store(Request $request, FortifyRegisteredUserController $controller)
    {
        return app()->call([$controller, 'store'], ['request' => $request]);
    }
}
