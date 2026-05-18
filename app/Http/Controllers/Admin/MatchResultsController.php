<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchResultsController extends Controller
{
    // ── Level 1: All Tournaments ──────────────────────────────────────────────
    public function index()
    {
        $tournaments = Tournament::withCount('seasons')->get();

        return view('Admin.match_results.index', compact('tournaments'));
    }

    // ── Level 2: Match Types (Singles / Doubles) ──────────────────────────────
    public function matchTypes(Tournament $tournament)
    {
        // Dynamically fetch distinct match types from whichever table this tournament uses
        $tableName  = $tournament->table_name ?? 'ekalavya_badmintion_tournament_s1';
        $matchTypes = DB::table($tableName)->distinct()->pluck('match_type')->filter()->values();

        // Fallback if table has no data yet
        if ($matchTypes->isEmpty()) {
            $matchTypes = collect(['Singles', 'Doubles']);
        }

        return view('Admin.match_results.match_type', compact('tournament', 'matchTypes'));
    }

    // ── Level 3: Age Categories ───────────────────────────────────────────────
    public function ageCategories(Tournament $tournament, string $matchType)
    {
        $tableName     = $tournament->table_name ?? 'ekalavya_badmintion_tournament_s1';
        $ageCategories = DB::table($tableName)
            ->where('match_type', $matchType)
            ->distinct()
            ->pluck('age_category')
            ->filter()
            ->sort()
            ->values();

        // Fallback
        if ($ageCategories->isEmpty()) {
            $ageCategories = collect(['U11', 'U13', 'U15', 'U19']);
        }

        return view('Admin.match_results.age_categories', compact(
            'tournament', 'matchType', 'ageCategories'
        ));
    }

    // ── Level 4: Player Results Table ─────────────────────────────────────────
    public function results(Tournament $tournament, string $matchType, string $ageCategory)
    {
        $tableName = $tournament->table_name ?? 'ekalavya_badmintion_tournament_s1';

        $players = DB::table($tableName)
            ->where('match_type', $matchType)
            ->where('age_category', $ageCategory)
            ->select('player_id', 'season_id', 'match_type', 'age_category',
                     'total_matches', 'won', 'lost')
            ->orderByDesc('won')
            ->get();

        // Summary stats
        $summary = [
    'total_players'  => $players->count(),
    'total_matches'  => $matchType === 'doubles'
                        ? $players->sum('total_matches') / 4  // 4 players per match
                        : $players->sum('total_matches') / 2, // 2 players per match
    'total_wins'     => $players->unique('season_id')->where('won', '>', 0)->count(),
    'total_losses'   => $players->unique('season_id')->where('lost', '>', 0)->count(),
];
        // $summary = [
        //     'total_players'  => $players->count(),
        //     'total_matches'  => $players->sum('total_matches'),
        //     'total_wins'     => $players->sum('won'),
        //     'total_losses'   => $players->sum('lost'),
        // ];

        return view('Admin.match_results.results', compact(
            'tournament', 'matchType', 'ageCategory', 'players', 'summary'
        ));
    }
}