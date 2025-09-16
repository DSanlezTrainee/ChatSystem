<?php

namespace App\Observers;

use App\Models\Room;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // Find the "All Talk" room or create it if it doesn't exist
        $allTalkRoom = Room::where('name', 'All Talk')->first();

        if (!$allTalkRoom) {
            // If "All Talk" room doesn't exist, create it
            $allTalkRoom = Room::create([
                'name' => 'All Talk',
                'owner_id' => 1, // Assuming user ID 1 is an admin
            ]);
        }

        // Add the user to the "All Talk" room
        $user->rooms()->attach($allTalkRoom->id);
    }
}
