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
    /**
     * Returns the current (pending) invite of the logged-in admin.
     * If the request is AJAX or wants JSON, returns the invite link and token as JSON.
     * Otherwise, loads rooms and users for the sidebar and renders the invite page.
     *
     * @return \Illuminate\Http\JsonResponse|\Inertia\Response
     */
    public function current()
    {
        $invite = ServerInvite::where('inviter_id', Auth::id())
            ->where('status', 'pending')
            ->latest('created_at')
            ->first();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'invite_link' => $invite ? url('/invite/' . $invite->token) : null,
                'token' => $invite ? $invite->token : null,
            ]);
        }
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

    /**
     * Generates a new general invite for the logged-in admin.
     * Invalidates all previous pending invites, creates a new invite, and returns the link and token.
     * Returns JSON for AJAX requests, otherwise redirects to the manage page.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function generate()
    {
        // Invalidates all previous invites of the admin
        ServerInvite::where('inviter_id', Auth::id())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        $token = Str::uuid()->toString();
        $invite = ServerInvite::create([
            'token' => $token,
            'inviter_id' => Auth::id(),
            'status' => 'pending',
        ]);

        if (request()->ajax() || request()->wantsJson() || request()->header('X-Inertia') === 'false') {
            return response()->json([
                'invite_link' => url('/invite/' . $invite->token),
                'token' => $invite->token,
            ]);
        }
        return redirect()->route('server-invite.manage')->with('success', 'New invite link generated successfully.');
    }

    /**
     * Accepts a general invite using the provided token.
     * Handles expired, invalid, or non-pending invites with appropriate HTTP errors.
     * Redirects to the registration page with the invite token if valid.
     *
     * @param string $token The invite token to accept.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept($token)
    {
        $invite = ServerInvite::where('token', $token)->first();

        // If the invite doesn't exist, return 404
        if (!$invite) {
            abort(404, 'Invite not found');
        }

        // If the status is expired, return 410 (Gone)
        if ($invite->status === 'expired') {
            abort(410, 'This invite link has expired');
        }

        // If the status is not pending, return 403
        if ($invite->status !== 'pending') {
            abort(403, 'This invite link is not valid');
        }

        return redirect('/register?invite_token=' . $token);
    }

    /**
     * Shows the page to manage server invites for the logged-in admin.
     * Loads pending and used invites, rooms, users, and server data for the sidebar.
     * Handles errors by logging and redirecting to the rooms index with an error message.
     *
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
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

            $user = Auth::user();
            $rooms = $user->rooms;
            $users = \App\Models\User::where('id', '!=', $user->id)->get(['id', 'name', 'avatar', 'status']);

            // Get the server data
            $server = \App\Models\Server::first();

            return Inertia::render('ServerInvite/Manage', [
                'pendingInvite' => $pendingInvite ? [
                    'token' => $pendingInvite->token,
                    'invite_link' => url('/invite/' . $pendingInvite->token),
                    'created_at' => $pendingInvite->created_at,
                ] : null,
                'usedInvites' => $usedInvites,
                'rooms' => $rooms,
                'users' => $users,
                'currentUser' => $user,
                'server' => $server
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading the pages with invites: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->route('rooms.index')->with('error', 'An error occurred while accessing the invites page.');
        }
    }
}
