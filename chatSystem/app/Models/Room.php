<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'avatar',
        'name',
        'owner_id',
    ];

    /**
     * Users that participate in the room.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\App\Models\User[]
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user')->withTimestamps();
    }

    /**
     * Messages in the room.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\App\Models\Message[]
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Invites for the room.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\App\Models\Invite[]
     */
    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * The owner of the room.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\App\Models\User
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
