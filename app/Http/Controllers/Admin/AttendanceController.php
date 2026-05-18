<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PlayerAttendance;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('Admin.attendance.index');
    }

    public function ages(string $mode)
    {
        $ages = ['U-11', 'U-13', 'U-15', 'U-19'];

        $ageCounts = DB::table('players')
            ->where('payment_status', 'paid')
            ->where('mode', $mode)
            ->whereIn('age', $ages)
            ->groupBy('age')
            ->selectRaw('age, count(*) as total')
            ->pluck('total', 'age')
            ->toArray();

        return view('Admin.attendance.ages', compact('mode', 'ages', 'ageCounts'));
    }


//     public function players(string $mode, string $age, Request $request)
// {
//     $date = $request->get('date', today()->toDateString());

//     // Check if a final match is already set up for this division+mode
//     $finalMatch = DB::table('matches')
//         ->where('division', $age)
//         ->where('round', 'final')
//         ->whereIn('status', ['setup', 'live', 'completed'])
//         ->first();

//     if ($finalMatch) {
//         // Only show the 2 finalists
//         $finalistIds = array_filter([
//             $finalMatch->player1_id,
//             $finalMatch->player2_id,
//         ]);

//         $players = DB::table('players')
//             ->where('players.payment_status', 'paid')
//             ->where('players.mode', $mode)
//             ->where('players.age', $age)
//             ->whereIn('players.player_id', $finalistIds)
//             ->leftJoin('player_attendance as pa', function ($join) use ($date) {
//                 $join->on('pa.player_id', '=', 'players.player_id')
//                      ->where('pa.date', '=', $date);
//             })
//             ->select(
//                 'players.player_id',
//                 'players.season_id',
//                 'players.name',
//                 'players.mode',
//                 'players.age',
//                 DB::raw('COALESCE(pa.is_present, 0) as is_present')
//             )
//             ->orderBy('players.name')
//             ->get();

//         return view('Admin.attendance.players', compact('players', 'mode', 'age', 'date'));
//     }

//     // No final yet — normal flow, exclude knockout losers
//     $losers = DB::table('matches')
//         ->where('status', 'completed')
//         ->where('group_type', 'knockout')
//         ->selectRaw('
//             CASE
//                 WHEN winner_id = player2_id THEN player1_id
//                 WHEN winner_id = player1_id THEN player2_id
//             END as loser_id
//         ')
//         ->pluck('loser_id')
//         ->filter()
//         ->toArray();

//     $query = DB::table('players')
//         ->where('players.payment_status', 'paid')
//         ->where('players.mode', $mode)
//         ->where('players.age', $age)
//         ->leftJoin('player_attendance as pa', function ($join) use ($date) {
//             $join->on('pa.player_id', '=', 'players.player_id')
//                  ->where('pa.date', '=', $date);
//         })
//         ->select(
//             'players.player_id',
//             'players.season_id',
//             'players.name',
//             'players.mode',
//             'players.age',
//             DB::raw('COALESCE(pa.is_present, 0) as is_present')
//         )
//         ->orderBy('players.name');

//     if (!empty($losers)) {
//         $query->whereNotIn('players.player_id', $losers);
//     }

//     $players = $query->get();

//     return view('Admin.attendance.players', compact('players', 'mode', 'age', 'date'));
// }



public function players(string $mode, string $age, Request $request)
{
    $date = $request->get('date', today()->toDateString());

    // Check if a final match is already set up for this division+mode
    $finalMatch = DB::table('matches')
        ->where('division', $age)
        ->where('round', 'final')
        ->where('match_type', $mode)
        ->whereIn('status', ['setup', 'live', 'completed'])
        ->first();

    if ($finalMatch) {
        // In doubles, player1_id/player2_id are Team IDs (season_id on players)
        // In singles, they are direct player_ids
        if ($mode === 'doubles') {
            $finalistTeamIds = array_filter([
                $finalMatch->player1_id,
                $finalMatch->player2_id,
            ]);

            $players = DB::table('players')
                ->where('players.payment_status', 'paid')
                ->where('players.mode', $mode)
                ->where('players.age', $age)
                ->whereIn('players.season_id', $finalistTeamIds)
                ->leftJoin('player_attendance as pa', function ($join) use ($date) {
                    $join->on('pa.player_id', '=', 'players.player_id')
                         ->where('pa.date', '=', $date);
                })
                ->select(
                    'players.player_id',
                    'players.season_id',
                    'players.name',
                    'players.mode',
                    'players.age',
                    DB::raw('COALESCE(pa.is_present, 0) as is_present')
                )
                ->orderBy('players.name')
                ->get();
        } else {
            $finalistIds = array_filter([
                $finalMatch->player1_id,
                $finalMatch->player2_id,
            ]);

            $players = DB::table('players')
                ->where('players.payment_status', 'paid')
                ->where('players.mode', $mode)
                ->where('players.age', $age)
                ->whereIn('players.player_id', $finalistIds)
                ->leftJoin('player_attendance as pa', function ($join) use ($date) {
                    $join->on('pa.player_id', '=', 'players.player_id')
                         ->where('pa.date', '=', $date);
                })
                ->select(
                    'players.player_id',
                    'players.season_id',
                    'players.name',
                    'players.mode',
                    'players.age',
                    DB::raw('COALESCE(pa.is_present, 0) as is_present')
                )
                ->orderBy('players.name')
                ->get();
        }

        return view('Admin.attendance.players', compact('players', 'mode', 'age', 'date'));
    }

    // No final yet — fetch knockout losers
    $losers = DB::table('matches')
        ->where('status', 'completed')
        ->where('group_type', 'knockout')
        ->where('match_type', $mode)
        ->selectRaw('
            CASE
                WHEN winner_id = player2_id THEN player1_id
                WHEN winner_id = player1_id THEN player2_id
            END as loser_id
        ')
        ->pluck('loser_id')
        ->filter()
        ->toArray();

    $query = DB::table('players')
        ->where('players.payment_status', 'paid')
        ->where('players.mode', $mode)
        ->where('players.age', $age)
        ->leftJoin('player_attendance as pa', function ($join) use ($date) {
            $join->on('pa.player_id', '=', 'players.player_id')
                 ->where('pa.date', '=', $date);
        })
        ->select(
            'players.player_id',
            'players.season_id',
            'players.name',
            'players.mode',
            'players.age',
            DB::raw('COALESCE(pa.is_present, 0) as is_present')
        )
        ->orderBy('players.name');

    if (!empty($losers)) {
        if ($mode === 'doubles') {
            // Doubles: loser IDs are Team IDs stored in season_id
            $query->whereNotIn('players.season_id', $losers);
        } else {
            // Singles: loser IDs are direct player_ids
            $query->whereNotIn('players.player_id', $losers);
        }
    }

    $players = $query->get();

    return view('Admin.attendance.players', compact('players', 'mode', 'age', 'date'));
}

    public function mark(Request $request)
    {
        $request->validate([
            'player_id'  => 'required',
            'is_present' => 'required|boolean',
            'date'       => 'required|date',
        ]);

        PlayerAttendance::updateOrCreate(
            [
                'player_id' => $request->player_id,
                'date'      => $request->date,
            ],
            [
                'is_present' => $request->is_present,
                'marked_at'  => now(),
                'marked_by'  => auth()->id(),
            ]
        );

        return response()->json(['success' => true]);
    }
}