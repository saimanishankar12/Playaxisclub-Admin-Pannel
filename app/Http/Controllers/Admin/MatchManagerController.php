<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\MatchSet;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchManagerController extends Controller
{
    private array $divisions   = ['U-11', 'U-13', 'U-15', 'U-19'];
    private array $roundLabels = [
        'quarter_final' => 'Knock Out',
        'semi_final'    => 'Semi Final',
        'final'         => 'Final',
    ];

    // ══════════════════════════════════════════════════════════════════════════
    //  PUBLIC ROUTES
    // ══════════════════════════════════════════════════════════════════════════

    public function index()
    {
        $matches = MatchGame::orderByDesc('created_at')->get()->map(function ($m) {
            $m->p1_name = $m->getPlayer1Name();
            $m->p2_name = $m->getPlayer2Name();
            return $m;
        });

        $liveMatches = $matches->where('status', 'live')->values();

        return view('Admin.matches.index', compact('matches', 'liveMatches'));
    }

    public function setup()
    {
        $courts  = DB::table('courts')->orderBy('court_no')->get();
        $umpires = DB::table('empires')->orderBy('name')->get();
        $scorers = DB::table('scorers')->orderBy('name')->get();

        $liveMatches = MatchGame::where('status', 'live')->get();
        $busyCourts  = $liveMatches->pluck('court_no')->toArray();
        $busyUmpires = $liveMatches->pluck('umpire_name')->toArray();
        $busyScorers = $liveMatches->pluck('scorer_name')->filter()->toArray();

        return view('Admin.matches.setup', [
            'divisions'   => $this->divisions,
            'roundLabels' => $this->roundLabels,
            'courts'      => $courts,
            'umpires'     => $umpires,
            'scorers'     => $scorers,
            'busyCourts'  => $busyCourts,
            'busyUmpires' => $busyUmpires,
            'busyScorers' => $busyScorers,
        ]);
    }

    public function storeSetup(Request $request)
    {
        $request->validate([
            'court_no'    => 'required|string|max:50',
            'umpire_name' => 'required|string|max:100',
            'scorer_name' => 'nullable|string|max:100',
            'match_type'  => 'required|in:singles,doubles',
            'division'    => 'required|in:U-11,U-13,U-15,U-19',
            'round'       => 'required|in:quarter_final,semi_final,final',
            'sets'        => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $round = $request->input('round');
                    $sets  = (int) $value;

                    if (!in_array($sets, [1, 3, 5])) {
                        $fail('Sets must be 1, 3, or 5.');
                        return;
                    }

                    if ($round === 'quarter_final' && $sets !== 1) {
                        $fail('Knockout rounds must be exactly 1 set.');
                        return;
                    }

                    if (in_array($round, ['semi_final', 'final']) && $sets < 3) {
                        $fail('Semi Finals and Finals must be 3 or more sets.');
                    }
                },
            ],
        ]);

        $matchType = $request->match_type;
        $division  = $request->division;
        $round     = $request->round;

        // $liveCount = MatchGame::where('status', 'live')
        //     ->where('match_type', $matchType)
        //     ->where('division', $division)
        //     ->count();

        // if ($liveCount > 0) {
        //     return back()
        //         ->withInput()
        //         ->withErrors(['division' => 'There are still ' . $liveCount . ' live match(es) in progress for ' . $division . ' ' . $matchType . '. Please complete them before starting a new match.']);
        // }

        $pair = $this->autoPairPlayers($matchType, $division, $round);

        if (!$pair) {
            $presentPlayerIds = DB::table('player_attendance')
                ->where('is_present', true)
                ->where('date', today())
                ->pluck('player_id')
                ->toArray();

            if ($matchType === 'singles') {
                $totalRegistered = DB::table('players')
                    ->where('mode', 'singles')
                    ->where('payment_status', 'paid')
                    ->where('age', $division)
                    ->count();

                $totalPresent = DB::table('players')
                    ->where('mode', 'singles')
                    ->where('payment_status', 'paid')
                    ->where('age', $division)
                    ->whereIn('player_id', $presentPlayerIds)
                    ->count();

                $message = "No eligible pair found for {$division} {$matchType}. "
                    . "{$totalPresent} of {$totalRegistered} players are present, "
                    . "but none are available to be paired right now.";
            } else {
                $totalTeams = DB::table('players')
                    ->where('mode', 'doubles')
                    ->where('payment_status', 'paid')
                    ->where('age', $division)
                    ->distinct()
                    ->count('season_id');

                $presentTeams = DB::table('players')
                    ->where('mode', 'doubles')
                    ->where('payment_status', 'paid')
                    ->where('age', $division)
                    ->whereIn('player_id', $presentPlayerIds)
                    ->groupBy('season_id')
                    ->havingRaw('COUNT(player_id) = 2')
                    ->get('season_id')
                    ->count();

                $partialTeams = DB::table('players')
                    ->where('mode', 'doubles')
                    ->where('payment_status', 'paid')
                    ->where('age', $division)
                    ->whereIn('player_id', $presentPlayerIds)
                    ->groupBy('season_id')
                    ->havingRaw('COUNT(player_id) = 1')
                    ->get('season_id')
                    ->count();

                $message = "No eligible pair found for {$division} {$matchType}. "
                    . "{$presentTeams} of {$totalTeams} teams are fully present "
                    . "({$partialTeams} team(s) have only 1 player present), "
                    . "but none are available to be paired right now.";
            }

            return back()
                ->withInput()
                ->withErrors(['division' => $message]);
        }

        [$player1Id, $player2Id, $groupType, $stageType] = $pair;

        session([
            'match_setup' => [
                'court_no'    => $request->court_no,
                'umpire_name' => $request->umpire_name,
                'scorer_name' => $request->scorer_name ?? '',
                'match_type'  => $matchType,
                'division'    => $division,
                'round'       => $request->round,
                'sets'        => (int) $request->sets,
                'group_type'  => $groupType,
                'stage_type'  => $stageType,
                'player1_id'  => $player1Id,
                'player2_id'  => $player2Id,
            ]
        ]);

        return redirect()->route('admin-matches.preview');
    }

    public function preview()
    {
        $setup = session('match_setup');

        if (!$setup) {
            return redirect()->route('admin-matches.setup')
                ->withErrors(['error' => 'Session expired. Please set up the match again.']);
        }

        $partner1 = null;
        $partner2 = null;

        if ($setup['match_type'] === 'singles') {
            $player1 = Player::where('player_id', $setup['player1_id'])->first();
            $player2 = Player::where('player_id', $setup['player2_id'])->first();
        } else {
            $player1  = Player::where('season_id', $setup['player1_id'])->where('mode', 'doubles')->first();
            $player2  = Player::where('season_id', $setup['player2_id'])->where('mode', 'doubles')->first();
            $partner1 = Player::where('season_id', $setup['player1_id'])->where('mode', 'doubles')->where('player_id', '!=', optional($player1)->player_id)->first();
            $partner2 = Player::where('season_id', $setup['player2_id'])->where('mode', 'doubles')->where('player_id', '!=', optional($player2)->player_id)->first();
        }

        if (!$player1 || !$player2) {
            return redirect()->route('admin-matches.setup')
                ->withErrors(['division' => 'Could not find matched players. Please try again.']);
        }

        return view('Admin.matches.preview', compact('setup', 'player1', 'player2', 'partner1', 'partner2'));
    }

    public function confirm()
    {
        $setup = session('match_setup');

        if (!$setup) {
            return redirect()->route('admin-matches.setup')
                ->withErrors(['error' => 'Session expired. Please set up the match again.']);
        }

        $totalSets = (int) ($setup['sets'] ?? 1);
        $setsToWin = (int) ceil($totalSets / 2);

        $match = MatchGame::create([
            'court_no'    => $setup['court_no'],
            'umpire_name' => $setup['umpire_name'],
            'scorer_name' => $setup['scorer_name'],
            'match_type'  => $setup['match_type'],
            'division'    => $setup['division'],
            'round'       => $setup['round'],
            'group_type'  => $setup['group_type'],
            'stage_type'  => $setup['stage_type'],
            'total_sets'  => $totalSets,
            'sets_to_win' => $setsToWin,
            'player1_id'  => $setup['player1_id'],
            'player2_id'  => $setup['player2_id'],
            'status'      => 'live',
            'started_at'  => now(),
        ]);

        session()->forget('match_setup');

        return redirect()->route('admin-matches.live', $match->id);
    }

    // public function live(MatchGame $match)
    // {
    //     if ($match->status === 'completed') {
    //         return redirect()->route('admin-matches.complete', $match->id);
    //     }

    //     $p1Name        = $match->getPlayer1Name();
    //     $p2Name        = $match->getPlayer2Name();
    //     $completedSets = $match->sets()->whereNotNull('winner')->get();

    //     return view('Admin.matches.live', compact('match', 'p1Name', 'p2Name', 'completedSets'));
    // }
public function live(MatchGame $match)
{
    if ($match->status === 'completed') {
        return redirect()->route('admin-matches.complete', $match->id);
    }

    $p1Name        = $match->getPlayer1Name();
    $p2Name        = $match->getPlayer2Name();
    $completedSets = $match->sets()->whereNotNull('winner')->get();

    $p1 = null;
    $p2 = null;

    if ($match->match_type === 'singles') {
        $p1 = DB::table('players')
            ->join('states', 'players.state_id', '=', 'states.id')
            ->where('players.player_id', $match->player1_id)
            ->select('players.*', 'states.name as state_name')
            ->first();

        $p2 = DB::table('players')
            ->join('states', 'players.state_id', '=', 'states.id')
            ->where('players.player_id', $match->player2_id)
            ->select('players.*', 'states.name as state_name')
            ->first();
    }

    return view('Admin.matches.live', compact('match', 'p1Name', 'p2Name', 'completedSets', 'p1', 'p2'));
}

//     public function live(MatchGame $match)
// {
//     if ($match->status === 'completed') {
//         return redirect()->route('admin-matches.complete', $match->id);
//     }

//     $p1Name        = $match->getPlayer1Name();
//     $p2Name        = $match->getPlayer2Name();
//     $completedSets = $match->sets()->whereNotNull('winner')->get();

//     if ($match->match_type === 'singles') {
//         $p1 = DB::table('players')
//             ->join('states', 'players.state_id', '=', 'states.id')
//             ->where('players.player_id', $match->player1_id)
//             ->select('players.*', 'states.name as state_name')
//             ->first();

//         $p2 = DB::table('players')
//             ->join('states', 'players.state_id', '=', 'states.id')
//             ->where('players.player_id', $match->player2_id)
//             ->select('players.*', 'states.name as state_name')
//             ->first();
//     }

//     return view('Admin.matches.live', compact('match', 'p1Name', 'p2Name', 'completedSets', 'p1', 'p2'));
// }

    public function updateScore(Request $request, MatchGame $match)
    {
        $request->validate(['action' => 'required|in:p1_plus,p2_plus,p1_minus,p2_minus']);

        if ($match->status === 'completed') {
            return response()->json(['error' => 'Match already completed.'], 400);
        }

        $s1 = $match->score_p1;
        $s2 = $match->score_p2;

        switch ($request->action) {
            case 'p1_plus':  $s1++; break;
            case 'p2_plus':  $s2++; break;
            case 'p1_minus': $s1 = max(0, $s1 - 1); break;
            case 'p2_minus': $s2 = max(0, $s2 - 1); break;
        }

        $match->score_p1 = $s1;
        $match->score_p2 = $s2;
        $match->save();

        $isJuice       = $match->isJuice();
        $isOBall       = ($s1 === 20 && $s2 === 0) || ($s2 === 20 && $s1 === 0);
        $completedSets = $match->sets()->whereNotNull('winner')->get();

        return response()->json([
            'score_p1'       => $s1,
            'score_p2'       => $s2,
            'sets_won_p1'    => $match->sets_won_p1,
            'sets_won_p2'    => $match->sets_won_p2,
            'current_set'    => $match->current_set,
            'is_juice'       => $isJuice,
            'is_oball'       => $isOBall,
            'set_winner'     => null,
            'match_winner'   => null,
            'status'         => $match->status,
            'completed_sets' => $completedSets->map(fn($s) => [
                'set_number' => $s->set_number,
                'score_p1'   => $s->score_p1,
                'score_p2'   => $s->score_p2,
                'winner'     => $s->winner,
            ]),
        ]);
    }

    public function declareWinner(Request $request, MatchGame $match)
    {
        $request->validate(['winner' => 'required|in:p1,p2']);

        $winnerId  = $request->winner === 'p1' ? $match->player1_id : $match->player2_id;
        $setWinner = $request->winner;

        $existingSet = MatchSet::where('match_id', $match->id)
            ->where('set_number', $match->current_set)
            ->first();

        if (!$existingSet) {
            MatchSet::create([
                'match_id'   => $match->id,
                'set_number' => $match->current_set,
                'score_p1'   => $match->score_p1,
                'score_p2'   => $match->score_p2,
                'winner'     => $setWinner,
            ]);
        } else {
            $existingSet->update([
                'score_p1' => $match->score_p1,
                'score_p2' => $match->score_p2,
                'winner'   => $setWinner,
            ]);
        }

        if ($request->winner === 'p1') $match->sets_won_p1++;
        else $match->sets_won_p2++;

        $matchOver = $match->sets_won_p1 >= $match->sets_to_win
            || $match->sets_won_p2 >= $match->sets_to_win;

        if ($matchOver) {
            $match->winner_id          = $winnerId;
            $match->declared_winner_id = $winnerId;
            $match->status             = 'completed';
            $match->completed_at       = now();
            $match->save();

            $this->updateTournamentStats($match, $request->winner);

            return response()->json([
                'match_over'  => true,
                'winner_name' => $request->winner === 'p1' ? $match->getPlayer1Name() : $match->getPlayer2Name(),
                'redirect'    => route('admin-matches.complete', $match->id),
            ]);
        }

        $match->current_set++;
        $match->score_p1 = 0;
        $match->score_p2 = 0;
        $match->save();

        $completedSets = $match->sets()->whereNotNull('winner')->get();

        return response()->json([
            'match_over'     => false,
            'sets_won_p1'    => $match->sets_won_p1,
            'sets_won_p2'    => $match->sets_won_p2,
            'current_set'    => $match->current_set,
            'score_p1'       => 0,
            'score_p2'       => 0,
            'completed_sets' => $completedSets->map(fn($s) => [
                'set_number' => $s->set_number,
                'score_p1'   => $s->score_p1,
                'score_p2'   => $s->score_p2,
                'winner'     => $s->winner,
            ]),
        ]);
    }

    public function editMatch(MatchGame $match)
    {
        $p1Name        = $match->getPlayer1Name();
        $p2Name        = $match->getPlayer2Name();
        $completedSets = $match->sets()->whereNotNull('winner')->get();

        return view('Admin.matches.results.edit', compact('match', 'p1Name', 'p2Name', 'completedSets'));
    }

    public function updateMatch(Request $request, MatchGame $match)
    {
        $request->validate([
            'sets'            => 'array',
            'sets.*.score_p1' => 'required|integer|min:0',
            'sets.*.score_p2' => 'required|integer|min:0',
            'sets.*.winner'   => 'required|in:p1,p2',
        ]);

        if ($request->sets) {
            foreach ($request->sets as $setNumber => $setData) {
                $scoreP1 = (int) $setData['score_p1'];
                $scoreP2 = (int) $setData['score_p2'];
                $winner  = $setData['winner'];

                if ($scoreP1 === 0 && $scoreP2 === 0) {
                    MatchSet::where('match_id', $match->id)
                        ->where('set_number', $setNumber)
                        ->update(['score_p1' => 0, 'score_p2' => 0, 'winner' => null]);
                    continue;
                }

                MatchSet::where('match_id', $match->id)
                    ->where('set_number', $setNumber)
                    ->update(['score_p1' => $scoreP1, 'score_p2' => $scoreP2, 'winner' => $winner]);
            }
        }

        $sets      = $match->sets()->whereNotNull('winner')->get();
        $setsWonP1 = $sets->where('winner', 'p1')->count();
        $setsWonP2 = $sets->where('winner', 'p2')->count();

        $match->sets_won_p1 = $setsWonP1;
        $match->sets_won_p2 = $setsWonP2;

        $isTied = ($setsWonP1 === $setsWonP2)
            && ($setsWonP1 > 0)
            && ($match->total_sets > 1);

        if ($isTied) {
            if ($match->winner_id) $this->reverseTournamentStats($match, $match->winner_id);

            $nextSet = $match->sets()->whereNotNull('winner')->max('set_number') + 1;
            MatchSet::where('match_id', $match->id)->whereNull('winner')->delete();
            MatchSet::create([
                'match_id'   => $match->id,
                'set_number' => $nextSet,
                'score_p1'   => 0,
                'score_p2'   => 0,
                'winner'     => null,
            ]);

            $match->winner_id          = null;
            $match->declared_winner_id = null;
            $match->status             = 'live';
            $match->current_set        = $nextSet;
            $match->score_p1           = 0;
            $match->score_p2           = 0;
            $match->completed_at       = null;
            $match->save();

            return redirect()->route('admin-matches.live', $match->id)
                ->with('success', 'Sets are tied! Set ' . $nextSet . ' created — continue scoring.');
        }

        $newSide = null; $newWinnerId = null;

        if ($request->filled('winner')) {
            $newSide     = $request->winner;
            $newWinnerId = $newSide === 'p1' ? $match->player1_id : $match->player2_id;
        } elseif ($setsWonP1 > $setsWonP2) {
            $newSide = 'p1'; $newWinnerId = $match->player1_id;
        } elseif ($setsWonP2 > $setsWonP1) {
            $newSide = 'p2'; $newWinnerId = $match->player2_id;
        }

        $oldWinnerId               = $match->winner_id;
        $match->winner_id          = $newWinnerId;
        $match->declared_winner_id = $newWinnerId;
        $match->status             = 'completed';
        $match->save();

        if ($oldWinnerId !== $newWinnerId) {
            if ($oldWinnerId) $this->reverseTournamentStats($match, $oldWinnerId);
            if ($newWinnerId && $newSide) $this->updateTournamentStats($match, $newSide);
        }

        return redirect()->route('admin-matches.complete', $match->id)
            ->with('success', 'Match updated successfully.');
    }

    public function forceEnd(MatchGame $match)
    {
        if ($match->status !== 'completed') {
            $match->update(['status' => 'completed', 'completed_at' => now()]);
        }
        return response()->json(['ok' => true]);
    }

    public function complete(MatchGame $match)
    {
        if ($match->status !== 'completed') {
            $match->update(['status' => 'completed', 'completed_at' => now()]);
        }

        $p1Name        = $match->getPlayer1Name();
        $p2Name        = $match->getPlayer2Name();
        $winnerName    = $match->winner_id
            ? ($match->winner_id === $match->player1_id ? $p1Name : $p2Name)
            : null;
        $completedSets = $match->sets()->whereNotNull('winner')->get();
        $duration      = $match->started_at && $match->completed_at
            ? $match->started_at->diffInMinutes($match->completed_at)
            : null;

        return view('Admin.matches.complete', compact(
            'match', 'p1Name', 'p2Name', 'winnerName', 'duration', 'completedSets'
        ));
    }

    public function results()
    {
        $matches = MatchGame::where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get()
            ->map(function ($m) {
                $m->p1_name     = $m->getPlayer1Name();
                $m->p2_name     = $m->getPlayer2Name();
                $m->winner_name = $m->winner_id === $m->player1_id ? $m->p1_name : $m->p2_name;
                return $m;
            });

        $liveMatches = collect();

        return view('Admin.matches.results.index', compact('matches', 'liveMatches'));
    }

    public function bracket(Request $request)
    {
        $matchType = $request->get('type', 'singles');
        $division  = $request->get('division', 'U-11');

        $matches = MatchGame::where('match_type', $matchType)
            ->where('division', $division)
            ->orderBy('id')
            ->get()
            ->map(function ($m) {
                $m->p1_name     = $m->getPlayer1Name();
                $m->p2_name     = $m->getPlayer2Name();
                $m->winner_name = $m->winner_id
                    ? ($m->winner_id === $m->player1_id ? $m->p1_name : $m->p2_name)
                    : null;
                $m->round_label = $m->getRoundLabel();
                return $m;
            })
            ->groupBy('round');

        return view('Admin.matches.bracket', compact('matches', 'matchType', 'division'));
    }

    public function eligibleCount(Request $request)
    {
        $matchType = $request->get('match_type');
        $division  = $request->get('division');

        if (!$matchType || !$division) {
            return response()->json(['count' => 0]);
        }

        $eliminatedIds = $this->getEliminatedIds($matchType, $division);

        $liveIds = MatchGame::where('status', 'live')
            ->where('match_type', $matchType)
            ->where('division', $division)
            ->get()
            ->flatMap(fn($m) => [$m->player1_id, $m->player2_id])
            ->filter()
            ->toArray();

        $presentPlayerIds = DB::table('player_attendance')
            ->where('is_present', true)
            ->where('date', today())
            ->pluck('player_id')
            ->toArray();

        // Calculate autoRound based on eligible count (without byeIds first)
        $baseExcludeIds = array_unique(array_merge($eliminatedIds, $liveIds));

        if ($matchType === 'singles') {
            $eligibleCountForRound = DB::table('players')
                ->where('mode', 'singles')
                ->where('payment_status', 'paid')
                ->where('age', $division)
                ->whereIn('player_id', $presentPlayerIds)
                ->whereNotIn('player_id', $baseExcludeIds)
                ->count();
        } else {
            $eligibleCountForRound = DB::table('players')
                ->where('mode', 'doubles')
                ->where('payment_status', 'paid')
                ->where('age', $division)
                ->whereIn('player_id', $presentPlayerIds)
                ->whereNotIn('season_id', $baseExcludeIds)
                ->groupBy('season_id')
                ->havingRaw('COUNT(player_id) = 2')
                ->count();
        }

        $autoRound = match(true) {
            $eligibleCountForRound <= 2 => 'final',
            $eligibleCountForRound <= 4 => 'semi_final',
            default                     => 'quarter_final',
        };

        // Get byeIds using autoRound (only for knockout)
        $byeIds = DB::table('match_byes')
            ->where('match_type', $matchType)
            ->where('division', $division)
            ->where('round', $autoRound)
            ->pluck('player_id')
            ->filter(function ($byePlayerId) use ($matchType, $division) {
                $byeRecord = DB::table('match_byes')
                    ->where('player_id', $byePlayerId)
                    ->where('match_type', $matchType)
                    ->where('division', $division)
                    ->orderByDesc('created_at')
                    ->first();

                if (!$byeRecord) return false;

                $matchesAfterBye = MatchGame::where('match_type', $matchType)
                    ->where('division', $division)
                    ->where('group_type', 'knockout')
                    ->where('status', 'completed')
                    ->where('created_at', '>', $byeRecord->created_at)
                    ->count();

                return $matchesAfterBye === 0;
            })
            ->toArray();

        $excludeIds = array_unique(array_merge($eliminatedIds, $liveIds, $byeIds));

        if ($matchType === 'singles') {
            $count = DB::table('players')
                ->where('mode', 'singles')
                ->where('payment_status', 'paid')
                ->where('age', $division)
                ->whereIn('player_id', $presentPlayerIds)
                ->whereNotIn('player_id', $excludeIds)
                ->count();
        } else {
            $count = DB::table('players')
                ->where('mode', 'doubles')
                ->where('payment_status', 'paid')
                ->where('age', $division)
                ->whereNotIn('season_id', $excludeIds)
                ->whereIn('player_id', $presentPlayerIds)
                ->groupBy('season_id')
                ->havingRaw('COUNT(player_id) = 2')
                ->get('season_id')
                ->count();
        }

        $stage = $this->determineStage($count);

        return response()->json([
            'count'   => $count,
            'canPair' => $count >= 2,
            'stage'   => $stage,
            'hasBye'  => ($count % 2 !== 0 && $stage === 'knockout'),
        ]);
    }




private function autoPairPlayers(string $matchType, string $division, string $round): ?array
{
    // ── Step 1: Get excluded player/team IDs ──────────────────────────
    $eliminatedIds = $this->getEliminatedIds($matchType, $division);

    $liveIds = MatchGame::where('status', 'live')
        ->where('match_type', $matchType)
        ->where('division', $division)
        ->get()
        ->flatMap(fn($m) => [$m->player1_id, $m->player2_id])
        ->filter()
        ->toArray();

    // ── Step 2: Get present player IDs from attendance ────────────────
    $presentPlayerIds = DB::table('player_attendance')
        ->where('is_present', true)
        ->where('date', today())
        ->pluck('player_id')
        ->toArray();

    // ── Step 3: Calculate autoRound based on eligible count ───────────
    $baseExcludeIds = array_unique(array_merge($eliminatedIds, $liveIds));

    if ($matchType === 'singles') {
        $eligibleCount = DB::table('players')
            ->where('mode', 'singles')
            ->where('payment_status', 'paid')
            ->where('age', $division)
            ->whereIn('player_id', $presentPlayerIds)
            ->whereNotIn('player_id', $baseExcludeIds)
            ->count();
    } else {
        $eligibleCount = DB::table('players')
            ->where('mode', 'doubles')
            ->where('payment_status', 'paid')
            ->where('age', $division)
            ->whereIn('player_id', $presentPlayerIds)
            ->whereNotIn('season_id', $baseExcludeIds)
            ->groupBy('season_id')
            ->havingRaw('COUNT(player_id) = 2')
            ->count();
    }

    $autoRound = match(true) {
        $eligibleCount <= 2 => 'final',
        $eligibleCount <= 4 => 'semi_final',
        default             => 'quarter_final',
    };

    // ── Step 4: Get byeIds using autoRound (knockout only) ────────────
    $byeIds = DB::table('match_byes')
        ->where('match_type', $matchType)
        ->where('division', $division)
        ->where('round', $autoRound)
        ->where('player_id', '!=', 'BATCH_MARKER')
        ->pluck('player_id')
        ->filter(function ($byePlayerId) use ($matchType, $division) {
            $byeRecord = DB::table('match_byes')
                ->where('player_id', $byePlayerId)
                ->where('match_type', $matchType)
                ->where('division', $division)
                ->orderByDesc('created_at')
                ->first();

            if (!$byeRecord) return false;

            $matchesAfterBye = MatchGame::where('match_type', $matchType)
                ->where('division', $division)
                ->where('group_type', 'knockout')
                ->where('status', 'completed')
                ->where('created_at', '>', $byeRecord->created_at)
                ->count();

            return $matchesAfterBye === 0;
        })
        ->toArray();

    $excludeIds = array_unique(array_merge($eliminatedIds, $liveIds, $byeIds));

    // ── Step 5: Get eligible players/teams ───────────────────────────
    if ($matchType === 'singles') {
        $allEligibleIds = DB::table('players')
            ->where('mode', 'singles')
            ->where('payment_status', 'paid')
            ->where('age', $division)
            ->whereIn('player_id', $presentPlayerIds)
            ->whereNotIn('player_id', $excludeIds)
            ->pluck('player_id')
            ->toArray();
    } else {
        $allEligibleIds = DB::table('players')
            ->where('mode', 'doubles')
            ->where('payment_status', 'paid')
            ->where('age', $division)
            ->whereNotIn('season_id', $excludeIds)
            ->whereIn('player_id', $presentPlayerIds)
            ->groupBy('season_id')
            ->havingRaw('COUNT(player_id) = 2')
            ->pluck('season_id')
            ->toArray();
    }

    // ── Step 6: Determine stage and group type ────────────────────────
    $total = count($allEligibleIds);
    if ($total < 2) return null;

    $stage     = $this->determineStage($total);
    $groupType = ($stage === 'round_robin') ? 'round_robin' : 'knockout';

    // If round_robin already started for this division/round, keep it round_robin
    $rrAlreadyStarted = MatchGame::where('match_type', $matchType)
        ->where('division', $division)
        ->where('group_type', 'round_robin')
        ->where('round', $autoRound)
        ->exists();

    if ($rrAlreadyStarted) {
        $stage     = 'round_robin';
        $groupType = 'round_robin';
    }

    // ── Step 7: Pair players ──────────────────────────────────────────
    if ($groupType === 'knockout') {
        // Assign bye if odd players
        if (count($allEligibleIds) % 2 !== 0) {
            $byePlayerId    = $this->assignByePlayer($allEligibleIds, $matchType, $division, $autoRound);
            $allEligibleIds = array_values(array_filter($allEligibleIds, fn($id) => $id !== $byePlayerId));
        }

        $playedInBatchIds = $this->getPlayedIdsInCurrentBatch($matchType, $division);
        $notYetPlayedIds  = array_values(array_diff($allEligibleIds, $playedInBatchIds));
        $playerIds        = count($notYetPlayedIds) >= 2 ? $notYetPlayedIds : $allEligibleIds;
    } else {
        // round_robin — NO bye for semis/finals
        $playerIds = $allEligibleIds;
    }

    return $this->pairByWins($playerIds, $matchType, $division, $round, $groupType, $stage);
}
    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS — PAIRING
    // ══════════════════════════════════════════════════════════════════════════
        
    private function determineStage(int $total): string
    {
        if ($total <= 1) return 'final';
        if ($total === 3) return 'round_robin';

        $stages = [
            2  => 'final',
            4  => 'semi_final',
            8  => 'quarter_final',
            16 => 'round_of_16',
            32 => 'round_of_32',
            64 => 'round_of_64',
        ];

        foreach ($stages as $threshold => $stage) {
            if ($total <= $threshold) return $stage;
        }

        return 'knockout';
    }


        private function getPlayedIdsInCurrentBatch(string $matchType, string $division): array
{
    // Get all knockout matches ordered by id
    $allKnockoutMatches = MatchGame::where('match_type', $matchType)
        ->where('division', $division)
        ->where('group_type', 'knockout')
        ->where('status', 'completed')
        ->orderBy('id')
        ->get();

    if ($allKnockoutMatches->isEmpty()) return [];

    // Split matches into batches
    // A new batch starts when a player appears for the second time
    $currentBatch  = [];
    $playedInBatch = [];

    foreach ($allKnockoutMatches as $match) {
        $p1 = $match->player1_id;
        $p2 = $match->player2_id;

        // If either player already played in current batch → new batch starts
        if (in_array($p1, $playedInBatch) || in_array($p2, $playedInBatch)) {
            $currentBatch  = [];
            $playedInBatch = [];
        }

        $currentBatch[]  = $match;
        $playedInBatch[] = $p1;
        $playedInBatch[] = $p2;
    }

    // Return players who played in the latest batch
    return collect($currentBatch)
        ->flatMap(fn($m) => [$m->player1_id, $m->player2_id])
        ->filter()
        ->unique()
        ->values()
        ->toArray();
}
    private function pairByWins(
        array  $playerIds,
        string $matchType,
        string $division,
        string $round,
        string $groupType,
        string $stageType
    ): ?array {
        if (count($playerIds) < 2) return null;

        $playedPairs = [];
        if ($groupType === 'round_robin') {
            $rrMatches = MatchGame::where('match_type', $matchType)
                ->where('division', $division)
                ->where('round', $round)
                ->where('group_type', 'round_robin')
                ->get();

            foreach ($rrMatches as $m) {
                $playedPairs[] = [$m->player1_id, $m->player2_id];
            }
        }

        $groups = [];
        foreach ($playerIds as $id) {
            $wins = DB::table('ekalavya_badmintion_tournament_s1')
                ->where('player_id', $id)
                ->where('match_type', $matchType)
                ->where('age_category', $division)
                ->value('won') ?? 0;
            $groups[$wins][] = $id;
        }
        ksort($groups);

        foreach ($groups as $ids) {
            shuffle($ids);
            for ($i = 0; $i < count($ids); $i++) {
                for ($j = $i + 1; $j < count($ids); $j++) {
                    if (!$this->hasPlayed($ids[$i], $ids[$j], $playedPairs)) {
                        return [$ids[$i], $ids[$j], $groupType, $stageType];
                    }
                }
            }
        }

        $flat = array_merge(...array_values($groups));
        for ($i = 0; $i < count($flat); $i++) {
            for ($j = $i + 1; $j < count($flat); $j++) {
                if (!$this->hasPlayed($flat[$i], $flat[$j], $playedPairs)) {
                    return [$flat[$i], $flat[$j], $groupType, $stageType];
                }
            }
        }

        return null;
    }

    private function hasPlayed(string $a, string $b, array $playedPairs): bool
    {
        foreach ($playedPairs as $pair) {
            if (in_array($a, $pair) && in_array($b, $pair)) {
                return true;
            }
        }
        return false;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS — BYE MANAGEMENT
    // ══════════════════════════════════════════════════════════════════════════

    private function assignByePlayer(
        array  $playerIds,
        string $matchType,
        string $division,
        string $round
    ): string {
        $existingBye = DB::table('match_byes')
            ->where('match_type', $matchType)
            ->where('division', $division)
            ->where('round', $round)
            ->first();

        if ($existingBye) {
            return $existingBye->player_id;
        }

        $byePlayerId = null;
        $maxWins     = -1;

        foreach ($playerIds as $id) {
            $wins = DB::table('ekalavya_badmintion_tournament_s1')
                ->where('player_id', $id)
                ->where('match_type', $matchType)
                ->where('age_category', $division)
                ->value('won') ?? 0;

            if ($wins > $maxWins) {
                $maxWins     = $wins;
                $byePlayerId = $id;
            }
        }

        DB::table('match_byes')->insert([
            'match_type'  => $matchType,
            'division'    => $division,
            'round'       => $round,
            'player_id'   => $byePlayerId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return $byePlayerId;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS — ELIMINATION TRACKING
    // ══════════════════════════════════════════════════════════════════════════

    private function getEliminatedIds(string $matchType, string $division): array
    {
        // Knockout losers
        $knockoutEliminated = MatchGame::where('match_type', $matchType)
            ->where('division', $division)
            ->where('status', 'completed')
            ->where('group_type', 'knockout')
            ->whereNotNull('winner_id')
            ->get()
            ->map(fn($m) => $m->winner_id === $m->player1_id ? $m->player2_id : $m->player1_id)
            ->filter()
            ->toArray();

        // Round robin eliminated (2 losses) and qualified (2 wins)
        $roundRobinEliminated = $this->getRoundRobinEliminated($matchType, $division);

        // Round robin qualifiers (2 wins) — exclude from further semi pairing
        $currentRound = MatchGame::where('match_type', $matchType)
            ->where('division', $division)
            ->where('group_type', 'round_robin')
            ->where('status', 'completed')
            ->orderByDesc('id')
            ->value('round');

        $roundRobinQualified = [];
        if ($currentRound) {
            $rrMatches = MatchGame::where('match_type', $matchType)
                ->where('division', $division)
                ->where('round', $currentRound)
                ->where('status', 'completed')
                ->where('group_type', 'round_robin')
                ->whereNotNull('winner_id')
                ->get();

            $stats = [];
            foreach ($rrMatches as $m) {
                $winnerId = $m->winner_id;
                $loserId  = $m->winner_id === $m->player1_id ? $m->player2_id : $m->player1_id;
                $stats[$winnerId]['wins']  = ($stats[$winnerId]['wins']  ?? 0) + 1;
                $stats[$loserId]['losses'] = ($stats[$loserId]['losses'] ?? 0) + 1;
            }

            foreach ($stats as $playerId => $stat) {
                if (($stat['wins'] ?? 0) >= 2) {
                    $roundRobinQualified[] = $playerId;
                }
            }
        }

        return array_unique(array_merge(
            $knockoutEliminated,
            $roundRobinEliminated,
            $roundRobinQualified
        ));
    }

      private function getRoundRobinEliminated(string $matchType, string $division): array
{
    $currentRound = MatchGame::where('match_type', $matchType)
        ->where('division', $division)
        ->where('group_type', 'round_robin')
        ->where('status', 'completed')
        ->orderByDesc('id')
        ->value('round');

    if (!$currentRound) return [];

    $rrMatches = MatchGame::where('match_type', $matchType)
        ->where('division', $division)
        ->where('round', $currentRound)
        ->where('status', 'completed')
        ->where('group_type', 'round_robin')
        ->whereNotNull('winner_id')
        ->get();

    if ($rrMatches->isEmpty()) return [];

    // Build stats
    $stats = [];
    foreach ($rrMatches as $m) {
        $winnerId = $m->winner_id;
        $loserId  = $m->winner_id === $m->player1_id ? $m->player2_id : $m->player1_id;

        $stats[$winnerId]['wins']   = ($stats[$winnerId]['wins']   ?? 0) + 1;
        $stats[$winnerId]['played'] = ($stats[$winnerId]['played'] ?? 0) + 1;
        $stats[$loserId]['losses']  = ($stats[$loserId]['losses']  ?? 0) + 1;
        $stats[$loserId]['played']  = ($stats[$loserId]['played']  ?? 0) + 1;

        // Calculate total points from match_sets
        $sets = DB::table('match_sets')->where('match_id', $m->id)->get();
        foreach ($sets as $set) {
            // p1 points
            $stats[$m->player1_id]['points'] = 
                ($stats[$m->player1_id]['points'] ?? 0) + $set->score_p1;
            // p2 points
            $stats[$m->player2_id]['points'] = 
                ($stats[$m->player2_id]['points'] ?? 0) + $set->score_p2;
        }
    }

    // ── Normal elimination: 2 losses ──────────────────────────────────
    $eliminated = [];
    foreach ($stats as $playerId => $stat) {
        if (($stat['losses'] ?? 0) >= 2) {
            $eliminated[] = $playerId;
            return $eliminated; // already found, no need for tiebreaker
        }
    }

    // ── Tie case: all 3 players have 1 win, 1 loss ────────────────────
    $allPlayed     = collect($stats)->every(fn($s) => ($s['played'] ?? 0) >= 2);
    $allEqualWins  = collect($stats)->every(fn($s) => ($s['wins'] ?? 0) === 1);

    if ($allPlayed && $allEqualWins && count($stats) === 3) {
        // Find player with lowest total points
        $lowestPoints  = PHP_INT_MAX;
        $lowestPlayer  = null;

        foreach ($stats as $playerId => $stat) {
            $points = $stat['points'] ?? 0;
            if ($points < $lowestPoints) {
                $lowestPoints = $points;
                $lowestPlayer = $playerId;
            }
        }

        if ($lowestPlayer) {
            $eliminated[] = $lowestPlayer;
        }
    }

    return $eliminated;
}
    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS — TOURNAMENT STATS
    // ══════════════════════════════════════════════════════════════════════════

    private function updateTournamentStats(MatchGame $match, string $winner): void
    {
        try {
            $winnerId = $winner === 'p1' ? $match->player1_id : $match->player2_id;
            $loserId  = $winner === 'p1' ? $match->player2_id : $match->player1_id;

            if ($match->match_type === 'singles') {
                $this->upsertStat($winnerId, $match->match_type, $match->division, 'won');
                $this->upsertStat($loserId,  $match->match_type, $match->division, 'lost');
            } else {
                foreach (DB::table('players')->where('season_id', $winnerId)->pluck('player_id') as $pid) {
                    $this->upsertStat($pid, $match->match_type, $match->division, 'won');
                }
                foreach (DB::table('players')->where('season_id', $loserId)->pluck('player_id') as $pid) {
                    $this->upsertStat($pid, $match->match_type, $match->division, 'lost');
                }
            }
        } catch (\Exception $e) {
            \Log::error('updateTournamentStats failed: ' . $e->getMessage());
        }
    }

    private function reverseTournamentStats(MatchGame $match, ?string $oldWinnerId): void
    {
        if (!$oldWinnerId) return;

        try {
            $oldLoserId = $oldWinnerId === $match->player1_id
                ? $match->player2_id
                : $match->player1_id;

            if ($match->match_type === 'singles') {
                $this->decrementStat($oldWinnerId, $match->match_type, $match->division, 'won');
                $this->decrementStat($oldLoserId,  $match->match_type, $match->division, 'lost');
            } else {
                foreach (DB::table('players')->where('season_id', $oldWinnerId)->pluck('player_id') as $pid) {
                    $this->decrementStat($pid, $match->match_type, $match->division, 'won');
                }
                foreach (DB::table('players')->where('season_id', $oldLoserId)->pluck('player_id') as $pid) {
                    $this->decrementStat($pid, $match->match_type, $match->division, 'lost');
                }
            }
        } catch (\Exception $e) {
            \Log::error('reverseTournamentStats failed: ' . $e->getMessage());
        }
    }

    private function upsertStat(string $playerId, string $matchType, string $division, string $result): void
    {
        if (empty($playerId)) return;

        $record = DB::table('ekalavya_badmintion_tournament_s1')
            ->where('player_id', $playerId)
            ->where('match_type', $matchType)
            ->where('age_category', $division)
            ->first();

        if ($record) {
            DB::table('ekalavya_badmintion_tournament_s1')
                ->where('id', $record->id)
                ->update([
                    'total_matches' => $record->total_matches + 1,
                    $result         => $record->{$result} + 1,
                    'updated_at'    => now(),
                ]);
        } else {
            $player = DB::table('players')->where('player_id', $playerId)->first();
            DB::table('ekalavya_badmintion_tournament_s1')->insert([
                'player_id'     => $playerId,
                'season_id'     => $player->season_id ?? null,
                'match_type'    => $matchType,
                'age_category'  => $division,
                'total_matches' => 1,
                'won'           => $result === 'won'  ? 1 : 0,
                'lost'          => $result === 'lost' ? 1 : 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    private function decrementStat(string $playerId, string $matchType, string $division, string $result): void
    {
        if (empty($playerId)) return;

        $record = DB::table('ekalavya_badmintion_tournament_s1')
            ->where('player_id', $playerId)
            ->where('match_type', $matchType)
            ->where('age_category', $division)
            ->first();

        if ($record) {
            DB::table('ekalavya_badmintion_tournament_s1')
                ->where('id', $record->id)
                ->update([
                    'total_matches' => max(0, $record->total_matches - 1),
                    $result         => max(0, $record->{$result} - 1),
                    'updated_at'    => now(),
                ]);
        }
    }
}