<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    // ── Show the match setup form ─────────────────────────────────────────────
    // public function index()
    // {
    //     $courts  = DB::table('courts')->orderBy('court_no')->get();
    //     $empires = DB::table('empires')->orderBy('name')->get();
    //     $scorers = DB::table('scorers')->orderBy('name')->get();

    //     // Seed courts/empires/scorers if tables are empty
    //     $this->seedIfEmpty();

    //     $courts  = DB::table('courts')->orderBy('court_no')->get();
    //     $empires = DB::table('empires')->orderBy('name')->get();
    //     $scorers = DB::table('scorers')->orderBy('name')->get();

    //     return view('Admin.play', compact('courts', 'empires', 'scorers'));
    // }


    public function index()
{
    $active = DB::table('active_matches')
        ->where('admin_id', session('admin_id'))
        ->first();

    if ($active) {
        $matchState = json_decode($active->match_state, true);
        session(['match_state' => $matchState]);
        return redirect()->back()
            ->with('warning', 'You have a live match in progress. Finish it first.');
    }

    $this->seedIfEmpty();

    $courts  = DB::table('courts')->orderBy('court_no')->get();
    $empires = DB::table('empires')->orderBy('name')->get();
    $scorers = DB::table('scorers')->orderBy('name')->get();

    return view('Admin.play', compact('courts', 'empires', 'scorers'));
}

//     public function index()
// {
//     // ── If admin has a live match, restore it and redirect ─────────────────
//     $active = DB::table('active_matches')
//         ->where('admin_id', auth()->id())
//         ->first();

//     if ($active) {
//         $matchState = json_decode($active->match_state, true);
//         session(['match_state' => $matchState]); // restore session
//         return redirect()->route('admin.match.live')
//             ->with('warning', 'You have a live match in progress. Finish it first.');
//     }

//     // ── Normal flow ────────────────────────────────────────────────────────
//     $this->seedIfEmpty();

//     $courts  = DB::table('courts')->orderBy('court_no')->get();
//     $empires = DB::table('empires')->orderBy('name')->get();
//     $scorers = DB::table('scorers')->orderBy('name')->get();

//     return view('Admin.play', compact('courts', 'empires', 'scorers'));
// }

    // ── Seed courts / empires / scorers if empty (no seeders) ─────────────────
    private function seedIfEmpty(): void
    {
        if (DB::table('courts')->count() === 0) {
            DB::table('courts')->insert([
                ['court_no' => 1], ['court_no' => 2],
                ['court_no' => 3], ['court_no' => 4],
            ]);
        }

        if (DB::table('empires')->count() === 0) {
            $now = now();
            DB::table('empires')->insert([
                ['name' => 'Rahul Sharma',  'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Priya Menon',   'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Arun Kumar',    'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Deepa Nair',    'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Suresh Reddy',  'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('scorers')->count() === 0) {
            $now = now();
            DB::table('scorers')->insert([
                ['name' => 'Vikram Iyer',   'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Sneha Pillai',  'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Arjun Das',     'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Kavya Rao',     'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    // ── Fetch random players based on age + mode ──────────────────────────────
    public function getPlayers(Request $request)
    {
        $request->validate([
            'age'  => 'required|in:U-13,U-15,U-19',
            'mode' => 'required|in:singles,doubles',
        ]);

        $age    = $request->age;
        $mode   = $request->mode;
        $needed = $mode === 'singles' ? 2 : 4;

        $players = DB::table('players')
            ->where('age', $age)
            ->where('mode', $mode)
            ->inRandomOrder()
            ->limit($needed)
            ->get(['player_id', 'name', 'age', 'mode']);

        if ($players->count() < $needed) {
            return response()->json([
                'success' => false,
                'message' => "Not enough {$age} {$mode} players. Need {$needed}, found {$players->count()}.",
            ], 422);
        }

        // Build team IDs  e.g. ALK001S, ALK002D
        $suffix   = strtoupper(substr($mode, 0, 1)); // S or D
        $teamData = [];

        if ($mode === 'singles') {
            $teamData = [
                'teamA' => ['id' => 'ALK' . str_pad($players[0]->player_id, 3, '0', STR_PAD_LEFT) . $suffix, 'players' => [$players[0]]],
                'teamB' => ['id' => 'ALK' . str_pad($players[1]->player_id, 3, '0', STR_PAD_LEFT) . $suffix, 'players' => [$players[1]]],
            ];
        } else {
            $teamData = [
                'teamA' => ['id' => 'ALK' . str_pad($players[0]->player_id, 3, '0', STR_PAD_LEFT) . $suffix, 'players' => [$players[0], $players[1]]],
                'teamB' => ['id' => 'ALK' . str_pad($players[2]->player_id, 3, '0', STR_PAD_LEFT) . $suffix, 'players' => [$players[2], $players[3]]],
            ];
        }

        return response()->json(['success' => true, 'teams' => $teamData]);
    }

    // ── Start match — store state in session ──────────────────────────────────
    // public function startMatch(Request $request)
    // {
    //     $request->validate([
    //         'court_id'      => 'required|integer',
    //         'empire_id'     => 'required|integer',
    //         'scorer_id'     => 'nullable|integer',
    //         'mode'          => 'required|in:singles,doubles',
    //         'age'           => 'required|in:U-13,U-15,U-19',
    //         'teamA_id'      => 'required|string',
    //         'teamB_id'      => 'required|string',
    //         'teamA_players' => 'required|array',
    //         'teamB_players' => 'required|array',
    //     ]);

    //     $court   = DB::table('courts')->find($request->court_id);
    //     $empire  = DB::table('empires')->find($request->empire_id);
    //     $scorer  = $request->scorer_id ? DB::table('scorers')->find($request->scorer_id) : null;

    //     $matchState = [
    //         'court_no'      => $court->court_no,
    //         'court_id'      => $request->court_id,
    //         'empire_id'     => $request->empire_id,
    //         'empire_name'   => $empire->name,
    //         'scorer_id'     => $request->scorer_id,
    //         'scorer_name'   => $scorer ? $scorer->name : null,
    //         'mode'          => $request->mode,
    //         'age'           => $request->age,
    //         'teamA_id'      => $request->teamA_id,
    //         'teamB_id'      => $request->teamB_id,
    //         'teamA_players' => $request->teamA_players,
    //         'teamB_players' => $request->teamB_players,
    //         'scoreA'        => 0,
    //         'scoreB'        => 0,
    //         'started_at'    => now()->format('d M Y, h:i A'),
    //         'status'        => 'ongoing',
    //         'winner'        => null,
    //     ];

    //     session(['match_state' => $matchState]);

    //     return response()->json(['success' => true, 'match' => $matchState]);
    // }

//     public function startMatch(Request $request)
// {
//     $request->validate([
//         'court_id'      => 'required|integer',
//         'empire_id'     => 'required|integer',
//         'scorer_id'     => 'nullable|integer',
//         'mode'          => 'required|in:singles,doubles',
//         'age'           => 'required|in:U-13,U-15,U-19',
//         'teamA_id'      => 'required|string',
//         'teamB_id'      => 'required|string',
//         'teamA_players' => 'required|array',
//         'teamB_players' => 'required|array',
//     ]);

//     // ── Block if admin already has a live match ────────────────────────────
//     $existing = DB::table('active_matches')
//         // ->where('admin_id', auth()->id())
//         ->where('admin_id', session('admin_id'))
//         ->first();

//     if ($existing) {
//         return response()->json([
//             'success' => false,
//             'message' => 'You already have a live match in progress. Finish it before starting a new one.',
//         ], 409);
//     }

//     $court  = DB::table('courts')->find($request->court_id);
//     $empire = DB::table('empires')->find($request->empire_id);
//     $scorer = $request->scorer_id ? DB::table('scorers')->find($request->scorer_id) : null;

//     $matchState = [
//         'court_no'      => $court->court_no,
//         'court_id'      => $request->court_id,
//         'empire_id'     => $request->empire_id,
//         'empire_name'   => $empire->name,
//         'scorer_id'     => $request->scorer_id,
//         'scorer_name'   => $scorer ? $scorer->name : null,
//         'mode'          => $request->mode,
//         'age'           => $request->age,
//         'teamA_id'      => $request->teamA_id,
//         'teamB_id'      => $request->teamB_id,
//         'teamA_players' => $request->teamA_players,
//         'teamB_players' => $request->teamB_players,
//         'scoreA'        => 0,
//         'scoreB'        => 0,
//         'started_at'    => now()->format('d M Y, h:i A'),
//         'status'        => 'ongoing',
//         'winner'        => null,
//     ];

//     // ── Save to DB (survives logout) ───────────────────────────────────────
//     DB::table('active_matches')->insert([
//         'admin_id'    => auth()->id(),
//         'match_state' => json_encode($matchState),
//         'created_at'  => now(),
//         'updated_at'  => now(),
//     ]);

//     session(['match_state' => $matchState]);

//     return response()->json(['success' => true, 'match' => $matchState]);
// }
public function startMatch(Request $request)
{
    \Log::info('admin_id from session: ' . session('admin_id'));
    $request->validate([
        'court_id'      => 'required|integer',
        'empire_id'     => 'required|integer',
        'scorer_id'     => 'nullable|integer',
        'mode'          => 'required|in:singles,doubles',
        'age'           => 'required|in:U-13,U-15,U-19',
        'teamA_id'      => 'required|string',
        'teamB_id'      => 'required|string',
        'teamA_players' => 'required|array',
        'teamB_players' => 'required|array',
    ]);

    $existing = DB::table('active_matches')
        ->where('admin_id', session('admin_id'))
        ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'You already have a live match in progress. Finish it before starting a new one.',
        ], 409);
    }

    $court  = DB::table('courts')->find($request->court_id);
    $empire = DB::table('empires')->find($request->empire_id);
    $scorer = $request->scorer_id ? DB::table('scorers')->find($request->scorer_id) : null;

    $matchState = [
        'court_no'      => $court->court_no,
        'court_id'      => $request->court_id,
        'empire_id'     => $request->empire_id,
        'empire_name'   => $empire->name,
        'scorer_id'     => $request->scorer_id,
        'scorer_name'   => $scorer ? $scorer->name : null,
        'mode'          => $request->mode,
        'age'           => $request->age,
        'teamA_id'      => $request->teamA_id,
        'teamB_id'      => $request->teamB_id,
        'teamA_players' => $request->teamA_players,
        'teamB_players' => $request->teamB_players,
        'scoreA'        => 0,
        'scoreB'        => 0,
        'started_at'    => now()->format('d M Y, h:i A'),
        'status'        => 'ongoing',
        'winner'        => null,
    ];

    DB::table('active_matches')->insert([
        'admin_id'    => session('admin_id'),
        'match_state' => json_encode($matchState),
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    session(['match_state' => $matchState]);

    return response()->json(['success' => true, 'match' => $matchState]);
}

    // ── Update score ──────────────────────────────────────────────────────────
    public function updateScore(Request $request)
    {
        $request->validate([
            'team' => 'required|in:A,B',
        ]);

        $state = session('match_state');
        if (!$state || $state['status'] !== 'ongoing') {
            return response()->json(['success' => false, 'message' => 'No active match.'], 422);
        }

        if ($request->team === 'A') {
            $state['scoreA']++;
        } else {
            $state['scoreB']++;
        }

        $sA = $state['scoreA'];
        $sB = $state['scoreB'];

        // Determine match status
        $alert   = null;
        $winner  = null;

        $maxScore = max($sA, $sB);
        $minScore = min($sA, $sB);

        if ($maxScore >= 21) {
            if ($maxScore - $minScore >= 2) {
                // Winner declared
                $winner = $sA > $sB ? 'A' : 'B';
                $state['winner'] = $winner;
                $state['status'] = 'finished';
            } elseif ($sA === $sB) {
                $alert = 'juice'; // equal after 20
            } else {
                $alert = 'oball'; // difference of 1 after 20
            }
        } elseif ($sA === 20 && $sB === 20) {
            $alert = 'oball'; // exactly 20-20
        }

        session(['match_state' => $state]);

        return response()->json([
            'success' => true,
            'scoreA'  => $state['scoreA'],
            'scoreB'  => $state['scoreB'],
            'alert'   => $alert,
            'winner'  => $winner,
            'winnerTeamId' => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
        ]);
    }

    // ── End match — save results ──────────────────────────────────────────────

    public function endMatch(Request $request)
{
    $state = session('match_state');
    if (!$state) {
        return response()->json(['success' => false, 'message' => 'No match in session.'], 422);
    }

    $now    = now();
    $mode   = $state['mode'];
    $winner = $state['winner'];

    if ($mode === 'singles') {
        $matchId = DB::table('matches_table_singles')->insertGetId([
            'team_a'     => $state['teamA_id'],
            'team_b'     => $state['teamB_id'],
            'player_a'   => $state['teamA_players'][0],
            'player_b'   => $state['teamB_players'][0],
            'score_a'    => $state['scoreA'],
            'score_b'    => $state['scoreB'],
            'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
            'division'   => $state['age'],
            'court_no'   => $state['court_no'],
            'empire_id'  => $state['empire_id'],
            'scorer_id'  => $state['scorer_id'],
            'played_at'  => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $playerA = DB::table('players')->where('name', $state['teamA_players'][0])->first();
        $playerB = DB::table('players')->where('name', $state['teamB_players'][0])->first();

        if ($winner && $playerA && $playerB) {
            $winnerId = $winner === 'A' ? $playerA->id : $playerB->id;
            $loserId  = $winner === 'A' ? $playerB->id : $playerA->id;
            DB::table('singles_won')->insert(['player_id' => $winnerId, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
            DB::table('singles_lost')->insert(['player_id' => $loserId,  'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
        }
    } else {
        $matchId = DB::table('matches_table_doubles')->insertGetId([
            'team_a'     => $state['teamA_id'],
            'team_b'     => $state['teamB_id'],
            'player_a1'  => $state['teamA_players'][0],
            'player_a2'  => $state['teamA_players'][1],
            'player_b1'  => $state['teamB_players'][0],
            'player_b2'  => $state['teamB_players'][1],
            'score_a'    => $state['scoreA'],
            'score_b'    => $state['scoreB'],
            'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
            'division'   => $state['age'],
            'court_no'   => $state['court_no'],
            'empire_id'  => $state['empire_id'],
            'scorer_id'  => $state['scorer_id'],
            'played_at'  => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $allPlayerNames = array_merge($state['teamA_players'], $state['teamB_players']);
        foreach ($allPlayerNames as $idx => $pname) {
            $p = DB::table('players')->where('name', $pname)->first();
            if (!$p) continue;
            $isTeamA  = $idx < 2;
            $isWinner = ($winner === 'A' && $isTeamA) || ($winner === 'B' && !$isTeamA);
            if ($isWinner) {
                DB::table('doubles_won')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
            } else {
                DB::table('doubles_lost')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    DB::table('active_matches')->where('admin_id', session('admin_id'))->delete();
    session()->forget('match_state');

    return response()->json(['success' => true, 'message' => 'Match saved successfully!']);
}
//     public function endMatch(Request $request)
// {
//     $state = session('match_state');
//     if (!$state) {
//         return response()->json(['success' => false, 'message' => 'No match in session.'], 422);
//     }

//     $now    = now();
//     $mode   = $state['mode'];
//     $winner = $state['winner'];

//     if ($mode === 'singles') {
//         $matchId = DB::table('matches_table_singles')->insertGetId([
//             'team_a'     => $state['teamA_id'],
//             'team_b'     => $state['teamB_id'],
//             'player_a'   => $state['teamA_players'][0],
//             'player_b'   => $state['teamB_players'][0],
//             'score_a'    => $state['scoreA'],
//             'score_b'    => $state['scoreB'],
//             'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
//             'division'   => $state['age'],
//             'court_no'   => $state['court_no'],
//             'empire_id'  => $state['empire_id'],
//             'scorer_id'  => $state['scorer_id'],
//             'played_at'  => $now,
//             'created_at' => $now,
//             'updated_at' => $now,
//         ]);

//         $playerA = DB::table('players')->where('name', $state['teamA_players'][0])->first();
//         $playerB = DB::table('players')->where('name', $state['teamB_players'][0])->first();

//         if ($winner && $playerA && $playerB) {
//             $winnerId = $winner === 'A' ? $playerA->id : $playerB->id;
//             $loserId  = $winner === 'A' ? $playerB->id : $playerA->id;
//             DB::table('singles_won')->insert(['player_id' => $winnerId, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
//             DB::table('singles_lost')->insert(['player_id' => $loserId,  'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
//         }
//     } else {
//         $matchId = DB::table('matches_table_doubles')->insertGetId([
//             'team_a'     => $state['teamA_id'],
//             'team_b'     => $state['teamB_id'],
//             'player_a1'  => $state['teamA_players'][0],
//             'player_a2'  => $state['teamA_players'][1],
//             'player_b1'  => $state['teamB_players'][0],
//             'player_b2'  => $state['teamB_players'][1],
//             'score_a'    => $state['scoreA'],
//             'score_b'    => $state['scoreB'],
//             'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
//             'division'   => $state['age'],
//             'court_no'   => $state['court_no'],
//             'empire_id'  => $state['empire_id'],
//             'scorer_id'  => $state['scorer_id'],
//             'played_at'  => $now,
//             'created_at' => $now,
//             'updated_at' => $now,
//         ]);

//         $allPlayerNames = array_merge($state['teamA_players'], $state['teamB_players']);
//         foreach ($allPlayerNames as $idx => $pname) {
//             $p = DB::table('players')->where('name', $pname)->first();
//             if (!$p) continue;
//             $isTeamA  = $idx < 2;
//             $isWinner = ($winner === 'A' && $isTeamA) || ($winner === 'B' && !$isTeamA);
//             if ($isWinner) {
//                 DB::table('doubles_won')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
//             } else {
//                 DB::table('doubles_lost')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
//             }
//         }
//     }

//     // ── Clear live match from DB and session ───────────────────────────────
//     DB::table('active_matches')->where('admin_id', auth()->id())->delete();
//     session()->forget('match_state');

//     return response()->json(['success' => true, 'message' => 'Match saved successfully!']);
// }
    // public function endMatch(Request $request)
    // {
    //     $state = session('match_state');
    //     if (!$state) {
    //         return response()->json(['success' => false, 'message' => 'No match in session.'], 422);
    //     }

    //     $now    = now();
    //     $mode   = $state['mode'];
    //     $winner = $state['winner']; // 'A' or 'B'

    //     if ($mode === 'singles') {
    //         $matchId = DB::table('matches_table_singles')->insertGetId([
    //             'team_a'     => $state['teamA_id'],
    //             'team_b'     => $state['teamB_id'],
    //             'player_a'   => $state['teamA_players'][0],
    //             'player_b'   => $state['teamB_players'][0],
    //             'score_a'    => $state['scoreA'],
    //             'score_b'    => $state['scoreB'],
    //             'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
    //             'division'   => $state['age'],
    //             'court_no'   => $state['court_no'],
    //             'empire_id'  => $state['empire_id'],
    //             'scorer_id'  => $state['scorer_id'],
    //             'played_at'  => $now,
    //             'created_at' => $now,
    //             'updated_at' => $now,
    //         ]);

    //         // Fetch player IDs
    //         $playerA = DB::table('players')->where('name', $state['teamA_players'][0])->first();
    //         $playerB = DB::table('players')->where('name', $state['teamB_players'][0])->first();

    //         if ($winner && $playerA && $playerB) {
    //             $winnerId = $winner === 'A' ? $playerA->id : $playerB->id;
    //             $loserId  = $winner === 'A' ? $playerB->id : $playerA->id;

    //             DB::table('singles_won')->insert(['player_id' => $winnerId, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
    //             DB::table('singles_lost')->insert(['player_id' => $loserId,  'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
    //         }
    //     } else {
    //         $matchId = DB::table('matches_table_doubles')->insertGetId([
    //             'team_a'     => $state['teamA_id'],
    //             'team_b'     => $state['teamB_id'],
    //             'player_a1'  => $state['teamA_players'][0],
    //             'player_a2'  => $state['teamA_players'][1],
    //             'player_b1'  => $state['teamB_players'][0],
    //             'player_b2'  => $state['teamB_players'][1],
    //             'score_a'    => $state['scoreA'],
    //             'score_b'    => $state['scoreB'],
    //             'winner'     => $winner ? ($winner === 'A' ? $state['teamA_id'] : $state['teamB_id']) : null,
    //             'division'   => $state['age'],
    //             'court_no'   => $state['court_no'],
    //             'empire_id'  => $state['empire_id'],
    //             'scorer_id'  => $state['scorer_id'],
    //             'played_at'  => $now,
    //             'created_at' => $now,
    //             'updated_at' => $now,
    //         ]);

    //         // Save won/lost for each player in the teams
    //         $allPlayerNames = array_merge($state['teamA_players'], $state['teamB_players']);
    //         foreach ($allPlayerNames as $idx => $pname) {
    //             $p = DB::table('players')->where('name', $pname)->first();
    //             if (!$p) continue;
    //             $isTeamA = $idx < 2;
    //             $isWinner = ($winner === 'A' && $isTeamA) || ($winner === 'B' && !$isTeamA);
    //             if ($isWinner) {
    //                 DB::table('doubles_won')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
    //             } else {
    //                 DB::table('doubles_lost')->insert(['player_id' => $p->id, 'match_id' => $matchId, 'created_at' => $now, 'updated_at' => $now]);
    //             }
    //         }
    //     }

    //     session()->forget('match_state');

    //     return response()->json(['success' => true, 'message' => 'Match saved successfully!']);
    // }

    // ── Get current match state (for page reload) ─────────────────────────────
    public function getState()
    {
        $state = session('match_state');
        return response()->json(['success' => true, 'match' => $state]);
    }
}