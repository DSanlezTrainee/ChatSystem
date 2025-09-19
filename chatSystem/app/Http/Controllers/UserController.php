<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        app(\Laravel\Jetstream\Contracts\DeletesUsers::class)->delete($user);
        return redirect()->back();
    }

    public function togglePermission($id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = User::find(Auth::id());

        //Only admins can change permissions
        if ($currentUser->permission !== 'admin') {
            return redirect()->back()->with('error', 'Only administrators can change permissions.');
        }

        // Don't allow the admin to remove themselves
        if ($currentUser->id === $targetUser->id) {
            return redirect()->back()->with('error', 'You cannot change your own permission.');
        }

        $targetUser->permission = $targetUser->permission === 'admin' ? 'user' : 'admin';
        $targetUser->save();

        return redirect()->back()->with('success', 'Permission changed successfully.');
    }
    
}
