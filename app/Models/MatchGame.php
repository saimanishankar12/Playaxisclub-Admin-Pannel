<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class MatchGame extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'court_no', 'umpire_name', 'scorer_name',
        'match_type', 'division', 'round',
        'group_type', 'match_group',
        'player1_id', 'player2_id',
        'sets_to_win', 'total_sets',
        'sets_won_p1', 'sets_won_p2',
        'current_set', 'score_p1', 'score_p2',
        'winner_id', 'declared_winner_id', 'status',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function sets(): HasMany
    {
        return $this->hasMany(MatchSet::class, 'match_id')->orderBy('set_number');
    }

    // ── Scoring Helpers ───────────────────────────────────────────────────────

    public static function setsToWinForRound(string $round): int
    {
        return in_array($round, ['semi_final', 'final']) ? 2 : 1;
    }

    public function maxSets(): int
    {
        return $this->sets_to_win === 2 ? 3 : 1;
    }

    /**
     * Juice alert: both players at 20+
     */
    public function isJuice(): bool
    {
        return $this->score_p1 >= 20 && $this->score_p2 >= 20;
    }

    /**
     * No auto-win detection — admin declares winner manually.
     * This just returns null always (kept for compatibility).
     */
    public function getCurrentSetWinner(): ?string
    {
        return null;
    }

    public function getMatchWinner(): ?string
    {
        if ($this->sets_won_p1 >= $this->sets_to_win) return 'p1';
        if ($this->sets_won_p2 >= $this->sets_to_win) return 'p2';
        return null;
    }

    // ── Display Helpers ───────────────────────────────────────────────────────

    public function getPlayer1Name(): string
    {
        return $this->getPlayerName($this->player1_id);
    }

    public function getPlayer2Name(): string
    {
        return $this->getPlayerName($this->player2_id);
    }

    private function getPlayerName(?string $id): string
    {
        if (!$id) return '—';

        if ($this->match_type === 'singles') {
            $p = DB::table('players')->where('player_id', $id)->first();
            return $p ? $p->name : $id;
        }

        $names = DB::table('players')
            ->where('season_id', $id)
            ->where('mode', 'doubles')
            ->pluck('name');
        return $names->implode(' & ') ?: $id;
    }

    public function getRoundLabel(): string
    {
        return match($this->round) {
            'quarter_final' => 'Quarter Final',
            'semi_final'    => 'Semi Final',
            'final'         => 'Final',
            default         => ucfirst($this->round),
        };
    }

    /**
     * Get win count for a player/pair in this division+type.
     */
    public static function getWinCount(string $playerId, string $matchType, string $division): int
    {
        return DB::table('ekalavya_badmintion_tournament_s1')
            ->where('player_id', $playerId)
            ->where('match_type', $matchType)
            ->where('age_category', $division)
            ->value('won') ?? 0;
    }
}