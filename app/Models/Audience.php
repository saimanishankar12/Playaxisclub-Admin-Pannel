<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    protected $fillable = [
        'audience_id',
        'name',
        'email',
        'phone',
        'city',
        'age',
        'tournament_season_id',
        'tournament_name',
        'is_winner',
        'won_day',
    ];

    protected $casts = [
        'is_winner' => 'boolean',
    ];

    // ── Auto-generate audience_id + resolve tournament_name before creating ───
    protected static function booted(): void
    {
        static::creating(function (Audience $audience) {

            // 1. Auto-generate unique ID
            if (empty($audience->audience_id)) {
                $audience->audience_id = self::generateUniqueId();
            }

            // 2. Auto-resolve tournament name
            if (empty($audience->tournament_name) && $audience->tournament_season_id) {
                $season = TournamentSeason::with('tournament')
                    ->find($audience->tournament_season_id);

                $audience->tournament_name = $season?->tournament?->name;
            }
        });
    }

    /**
     * Generate a unique audience ID in the format AUD0001
     *
     * Format: AUD + 4-digit zero-padded number
     * Example: AUD0001, AUD0002, ..., AUD9999
     *
     * Max capacity: 9999 unique IDs
     */
    public static function generateUniqueId(): string
    {
        do {
            $last = self::orderByDesc('id')->lockForUpdate()->first();

            if (!$last || !$last->audience_id) {
                $number = 1;
            } else {
                preg_match('/AUD(\d{4})/', $last->audience_id, $matches);
                $number = $matches ? (int) $matches[1] + 1 : 1;
            }

            $candidateId = 'AUD' . str_pad($number, 4, '0', STR_PAD_LEFT);

        } while (self::where('audience_id', $candidateId)->exists());

        return $candidateId;
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function season()
    {
        return $this->belongsTo(TournamentSeason::class, 'tournament_season_id');
    }
}