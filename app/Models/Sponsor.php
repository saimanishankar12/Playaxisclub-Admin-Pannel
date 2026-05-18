<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $table = 'sponsors';

    protected $fillable = [
        'name', 'package', 'tournament_id', 'tournament_season_id', 'notes',
    ];

    protected $casts = [
        'package' => 'integer',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function season()
    {
        return $this->belongsTo(TournamentSeason::class, 'tournament_season_id');
    }
}
