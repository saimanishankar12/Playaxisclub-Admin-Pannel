<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoublesPair extends Model
{
    use HasFactory;
    protected $table = 'doubles';

    protected $fillable = [
        // 'doubles_pair_id',
        'season_id',
        'player1_id',
        'player2_id',
    ];

    // protected $table = 'doubles_pair';

    // protected $fillable = [
    //     'doubles_id',
    //     'season_id',
    //     'player1_id',
    //     'player2_id',
    // ];

    /* ── Relationships ── */

    public function player1()
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }
}