<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchSet extends Model
{
    protected $table = 'match_sets';

    protected $fillable = [
        'match_id', 'set_number',
        'score_p1', 'score_p2', 'winner',
    ];
}
