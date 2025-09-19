<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ServerController extends Controller
{
    /**
     * Displays a list of all servers.
     *
     * @return \Illuminate\Http\Response|void
     */
    public function index()
    {
        // Implement logic to list servers here
    }

    /**
     * Shows the form for creating a new server.
     *
     * @return \Illuminate\Http\Response|void
     */
    public function create()
    {
        // Implement logic to show server creation form here
    }

    /**
     * Stores a newly created server in the database.
     *
     * @param Request $request The HTTP request containing server data.
     * @return \Illuminate\Http\Response|void
     */
    public function store(Request $request)
    {
        // Implement logic to store a new server here
    }

    /**
     * Displays the details of a specific server.
     *
     * @param Server $server The server to display.
     * @return \Illuminate\Http\Response|void
     */
    public function show(Server $server)
    {
        // Implement logic to show server details here
    }

    /**
     * Shows the form for editing a specific server.
     *
     * @param Server $server The server to edit.
     * @return \Illuminate\Http\Response|void
     */
    public function edit(Server $server)
    {
        // Implement logic to show server edit form here
    }

    /**
     * Updates the specified server in the database.
     * Validates input, updates the server's name and avatar, and returns a JSON response or redirects.
     *
     * @param Request $request The HTTP request containing updated server data.
     * @param Server $server The server to update.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Server $server)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('servers', 'public');
            $server->avatar = '/storage/' . $path;
        }
        $server->name = $request->input('name');
        $server->save();

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Inertia') === 'false') {
            return response()->json([
                'success' => true,
                'server' => $server->fresh()
            ]);
        }

        return redirect()->route('server-invite.manage')->with('success', 'Server name updated successfully');
    }


    /**
     * Removes the specified server from the database.
     *
     * @param Server $server The server to delete.
     * @return \Illuminate\Http\Response|void
     */
    public function destroy(Server $server)
    {
        // Implement logic to delete a server here
    }
}
