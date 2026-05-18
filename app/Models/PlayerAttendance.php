<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerAttendance extends Model
{
    protected $table = 'player_attendance';
protected $fillable = ['player_id',  'date', 'season_id', 'is_present', 'marked_at', 'marked_by'];
}
