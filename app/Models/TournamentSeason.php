<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSeason extends Model
{
    protected $table = 'tournament_seasons';

    protected $fillable = [
        'tournament_id',
         'season_number',
          'label',
        'status', 
        'start_date',
         'end_date',
          'venue',
           'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function audiences()
    {
        return $this->hasMany(Audience::class, 'tournament_season_id');
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'tournament_season_id');
    }

    // Revenue for this specific season
    public function getRevenueAttribute(): float
    {
        // season_id in payments_data stores labels like ALK0080S, ALK0001D
        // We use the label prefix to match.  Adjust if your season labels differ.
        return Payment::where('status', 'paid')
            ->where(function ($q) {
                $q->where('season_id', 'like', $this->label . '%');
            })
            ->sum('amount');
    }
}
