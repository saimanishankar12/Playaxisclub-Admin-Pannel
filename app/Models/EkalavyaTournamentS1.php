<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkalavyaTournamentS1 extends Model
{
     protected $table = 'ekalavya_badminton_tournament_s1';

    protected $fillable = [
        'player_id', 'season_id', 'match_type',
        'age_category', 'total_matches', 'won', 'lost',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'player_id');
    }
}
