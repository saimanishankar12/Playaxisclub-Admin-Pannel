<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['sport_id', 'name', 'slug'];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function seasons()
    {
        return $this->hasMany(TournamentSeason::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    // Total revenue across ALL seasons of this tournament
    public function getTotalRevenueAttribute(): float
    {
        return Payment::where('status', 'paid')
            ->whereIn('season_id', $this->seasons->pluck('label'))
            ->sum('amount');
    }
}
