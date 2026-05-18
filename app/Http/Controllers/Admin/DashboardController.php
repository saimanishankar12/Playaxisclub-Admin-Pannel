<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Payment;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function updateSeasonStatus(Request $request, TournamentSeason $season)
    {
        $request->validate([
            'status' => 'required|in:upcoming,active,completed',
        ]);

        DB::table('seasons')
            ->where('id', $season->tournament_id)
            ->update(['status' => $request->status]);

        return redirect()->route('admin-dashboard')->with('success', 'Season status updated!');
    }

    public function storeSeason(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'season_number' => 'required|integer|min:1',
            'label'         => 'required|string|max:255',
            'status'        => 'required|in:upcoming,active,completed',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'venue'         => 'nullable|string|max:255',
            'description'   => 'nullable|string',
        ]);

        TournamentSeason::create($request->only(
            'tournament_id', 'season_number', 'label', 'status',
            'start_date', 'end_date', 'venue', 'description'
        ));

        return redirect()->route('admin-dashboard')->with('success', 'Tournament season added successfully!');
    }

    public function index()
    {
        $totalPlayers  = Player::count();
        $totalEarnings = Payment::where('status', 'paid')->sum('amount');

        // ── Attendance stats ──────────────────────────────────────────────────
        $totalSinglesPlayers = DB::table('players')
            ->where('mode', 'singles')
            ->where('payment_status', 'paid')
            ->count();

        $presentSinglesPlayers = DB::table('player_attendance')
            ->join('players', 'player_attendance.player_id', '=', 'players.player_id')
            ->where('players.mode', 'singles')
            ->where('players.payment_status', 'paid')
            ->where('player_attendance.is_present', true)
            ->where('player_attendance.date', today())
            ->count();

        $totalDoublesTeams = DB::table('players')
            ->where('mode', 'doubles')
            ->where('payment_status', 'paid')
            ->distinct()
            ->count('season_id');

        $presentDoublesTeams = DB::table('players')
            ->where('mode', 'doubles')
            ->where('payment_status', 'paid')
            ->whereIn('player_id', function ($q) {
                $q->select('player_id')
                    ->from('player_attendance')
                    ->where('is_present', true)
                    ->where('date', today());
            })
            ->groupBy('season_id')
            ->havingRaw('COUNT(player_id) = 2')
            ->get('season_id')
            ->count();

        // ── Seasons ───────────────────────────────────────────────────────────
        $seasons = DB::table('tournament_seasons')
            ->join('seasons', 'tournament_seasons.tournament_id', '=', 'seasons.id')
            ->select(
                'tournament_seasons.id',
                'tournament_seasons.label',
                'tournament_seasons.venue',
                'tournament_seasons.tournament_id',
                'seasons.name as tournament_name',
                'seasons.sport as tournament_sport',
                'seasons.start_date',
                'seasons.end_date',
                'seasons.status',
            )
            ->orderByDesc('tournament_seasons.id')
            ->get();

        $tournaments = DB::table('seasons')->orderBy('name')->get();

        // ── Live Matches ──────────────────────────────────────────────────────
        $liveMatches = MatchGame::where('status', 'live')
            ->orderByDesc('started_at')
            ->get()
            ->map(fn($m) => $this->enrichMatch($m));

        // ── Recent Completed ──────────────────────────────────────────────────
        $recentMatches = MatchGame::where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get()
            ->map(function ($m) {
                $m->p1_name     = $m->getPlayer1Name();
                $m->p2_name     = $m->getPlayer2Name();
                $m->winner_name = $m->winner_id === $m->player1_id
                    ? $m->p1_name
                    : ($m->winner_id ? $m->p2_name : null);
                return $m;
            });

        // ── Winners — final round completed matches with a declared winner ────
        // Groups by division + match_type, picks the final-round winner
        $winnerMatches = MatchGame::where('status', 'completed')
            ->whereNotNull('declared_winner_id')
            ->where('round', 'final')
            ->orderByDesc('completed_at')
            ->get()
            ->map(function ($m) {
                $isP1Winner = $m->declared_winner_id === $m->player1_id;

                if ($m->match_type === 'singles') {
                    $winner = DB::table('players')
                        ->where('player_id', $isP1Winner ? $m->player1_id : $m->player2_id)
                        ->first();

                    return (object) [
                        'match_id'    => $m->id,
                        'match_type'  => 'singles',
                        'division'    => $m->division,
                        'winner_name' => $winner->name      ?? '—',
                        'winner_id'   => $winner->player_id ?? '—',
                        'season_id'   => $winner->season_id ?? '—',
                        'partner'     => null,
                    ];
                }

                // Doubles — winner_id is a season_id (team id)
                $winnerTeamId = $isP1Winner ? $m->player1_id : $m->player2_id;
                $teamPlayers  = DB::table('players')
                    ->where('season_id', $winnerTeamId)
                    ->where('mode', 'doubles')
                    ->get();

                $p1 = $teamPlayers->first();
                $p2 = $teamPlayers->skip(1)->first();

                return (object) [
                    'match_id'    => $m->id,
                    'match_type'  => 'doubles',
                    'division'    => $m->division,
                    'winner_name' => $p1->name ?? '—',
                    'winner_id'   => $p1->player_id ?? '—',
                    'season_id'   => $winnerTeamId,
                    'partner'     => $p2 ? (object)[
                        'name'      => $p2->name,
                        'player_id' => $p2->player_id,
                    ] : null,
                ];
            })
            // Keep only one winner per division+type combination
            ->unique(fn($w) => $w->division . '_' . $w->match_type)
            ->values();

        return view('Admin.dashboard', compact(
            'totalPlayers',
            'totalEarnings',
            'seasons',
            'tournaments',
            'liveMatches',
            'recentMatches',
            'winnerMatches',
            'totalSinglesPlayers',
            'presentSinglesPlayers',
            'totalDoublesTeams',
            'presentDoublesTeams'
        ));
    }

    // ── AJAX: Live scores poll endpoint ──────────────────────────────────────
    public function liveScores()
    {
        $liveMatches = MatchGame::where('status', 'live')
            ->orderByDesc('started_at')
            ->get()
            ->map(fn($m) => $this->enrichMatch($m, true));

        return response()->json(['matches' => $liveMatches]);
    }

    // ── Private: enrich a match with full player details ─────────────────────
    private function enrichMatch(MatchGame $m, bool $asArray = false)
    {
        if ($m->match_type === 'singles') {
            $p1 = DB::table('players')->where('player_id', $m->player1_id)->first();
            $p2 = DB::table('players')->where('player_id', $m->player2_id)->first();

            $p1Info = [
                'id'        => $p1->player_id ?? '—',
                'season_id' => $p1->season_id ?? '—',
                'name'      => $p1->name      ?? $m->getPlayer1Name(),
                'city'      => $p1->address   ?? '—',
                'team_id'   => null,
                'partner'   => null,
            ];
            $p2Info = [
                'id'        => $p2->player_id ?? '—',
                'season_id' => $p2->season_id ?? '—',
                'name'      => $p2->name      ?? $m->getPlayer2Name(),
                'city'      => $p2->address   ?? '—',
                'team_id'   => null,
                'partner'   => null,
            ];
        } else {
            $team1Players = DB::table('players')
                ->where('season_id', $m->player1_id)
                ->where('mode', 'doubles')
                ->get();
            $team2Players = DB::table('players')
                ->where('season_id', $m->player2_id)
                ->where('mode', 'doubles')
                ->get();

            $t1p1 = $team1Players->first();
            $t1p2 = $team1Players->skip(1)->first();
            $t2p1 = $team2Players->first();
            $t2p2 = $team2Players->skip(1)->first();

            $p1Info = [
                'id'        => $t1p1->player_id ?? '—',
                'season_id' => $t1p1->season_id ?? '—',
                'name'      => $t1p1->name      ?? 'Team 1',
                'city'      => $t1p1->address   ?? '—',
                'team_id'   => $m->player1_id,
                'partner'   => $t1p2 ? [
                    'id'   => $t1p2->player_id ?? '—',
                    'name' => $t1p2->name      ?? '—',
                    'city' => $t1p2->address   ?? '—',
                ] : null,
            ];
            $p2Info = [
                'id'        => $t2p1->player_id ?? '—',
                'season_id' => $t2p1->season_id ?? '—',
                'name'      => $t2p1->name      ?? 'Team 2',
                'city'      => $t2p1->address   ?? '—',
                'team_id'   => $m->player2_id,
                'partner'   => $t2p2 ? [
                    'id'   => $t2p2->player_id ?? '—',
                    'name' => $t2p2->name      ?? '—',
                    'city' => $t2p2->address   ?? '—',
                ] : null,
            ];
        }

        if ($asArray) {
            return [
                'id'          => $m->id,
                'p1_id'       => $p1Info['id'],
                'p2_id'       => $p2Info['id'],
                'p1_name'     => $p1Info['name'],
                'p2_name'     => $p2Info['name'],
                'p1_info'     => $p1Info,
                'p2_info'     => $p2Info,
                'score_p1'    => $m->score_p1,
                'score_p2'    => $m->score_p2,
                'sets_won_p1' => $m->sets_won_p1,
                'sets_won_p2' => $m->sets_won_p2,
                'current_set' => $m->current_set,
                'total_sets'  => $m->total_sets,
                'match_type'  => $m->match_type,
                'division'    => $m->division,
                'round_label' => $m->getRoundLabel(),
                'court_no'    => $m->court_no,
                'status'      => $m->status,
                'started_at'  => $m->started_at?->toISOString(),
            ];
        }

        $m->p1_id   = $p1Info['id'];
        $m->p2_id   = $p2Info['id'];
        $m->p1_name = $p1Info['name'];
        $m->p2_name = $p2Info['name'];
        $m->p1_info = $p1Info;
        $m->p2_info = $p2Info;
        return $m;
    }
}