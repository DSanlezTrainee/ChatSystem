<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'avatar',
        'name',
    ];

    /**
     * Users that participate in the room.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user')->withTimestamps();
    }

    /**
     * Messages in the room.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Invites for the room.
     */
    public function invites()
    {
        return $this->hasMany(Invite::class);
    }
}
