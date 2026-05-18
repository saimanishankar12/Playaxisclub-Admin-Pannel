<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerOtp extends Model
{
    protected $table='players_otp';
     protected $fillable = ['player_id', 'otp', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
