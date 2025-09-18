<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomUser extends Pivot
{
    use HasFactory;

    // Garantir que joined_at seja tratado como uma data
    protected $casts = [
        'joined_at' => 'datetime',
    ];
}
