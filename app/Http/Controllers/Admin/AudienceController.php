<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use App\Models\Tournament;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;

class AudienceController extends Controller
{
    // ── List all audience + lucky draw winners ────────────────────────────────
    public function index(Request $request)
    {
        $tournaments  = Tournament::with('seasons')->get();
        $seasonId     = $request->get('tournament_season_id');

        $audiences = Audience::with('season')
            ->when($seasonId, fn($q) => $q->where('tournament_season_id', $seasonId))
            ->orderByDesc('created_at')
            ->paginate(25);

        // Winners already drawn per day
        $winners = Audience::where('is_winner', true)
            ->when($seasonId, fn($q) => $q->where('tournament_season_id', $seasonId))
            ->orderBy('won_day')
            ->get();

        // Which days already have a winner declared
        $wonDays = $winners->pluck('won_day')->toArray();

        return view('Admin.audience.index', compact(
            'tournaments', 'seasonId', 'audiences', 'winners', 'wonDays'
        ));
    }

    // ── Register a new audience member ───────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'nullable|email',
            'phone'                  => 'required|string|max:15',
            'city'                   => 'required|string|max:100',
            'age'                    => 'required|integer|min:1|max:120',
            'tournament_season_id'   => 'required|exists:tournament_seasons,id',
        ]);

        Audience::create($request->only(
            'name', 'email', 'phone', 'city', 'age', 'tournament_season_id'
        ));

        return redirect()->route('admin-audience')
            ->with('success', 'Audience member registered!');
    }

    // ── Lucky draw: declare winner by entering audience ID manually ───────────
    public function declareWinner(Request $request)
    {
        $request->validate([
            'audience_id'          => 'required|string',
            'won_day'              => 'required|integer|in:1,2,3',
            'tournament_season_id' => 'required|exists:tournament_seasons,id',
        ]);

        $seasonId   = $request->tournament_season_id;
        $day        = $request->won_day;
        $audienceId = strtoupper(trim($request->audience_id));

        // Check if a winner is already declared for this day
        $alreadyDeclared = Audience::where('is_winner', true)
            ->where('tournament_season_id', $seasonId)
            ->where('won_day', $day)
            ->exists();

        if ($alreadyDeclared) {
            return redirect()->route('admin-audience', ['tournament_season_id' => $seasonId])
                ->with('error', "A winner for Day {$day} has already been declared.");
        }

        // Find the audience member
        $member = Audience::where('audience_id', $audienceId)
            ->where('tournament_season_id', $seasonId)
            ->first();

        if (!$member) {
            return redirect()->route('admin-audience', ['tournament_season_id' => $seasonId])
                ->with('error', "Audience ID '{$audienceId}' not found in this tournament.");
        }

        // Check if this person already won on a different day
        if ($member->is_winner) {
            return redirect()->route('admin-audience', ['tournament_season_id' => $seasonId])
                ->with('error', "{$member->name} ({$audienceId}) has already won on Day {$member->won_day} and is not eligible again.");
        }

        // Declare winner
        $member->update([
            'is_winner' => true,
            'won_day'   => $day,
        ]);

        return redirect()->route('admin-audience', ['tournament_season_id' => $seasonId])
            ->with('success', "🎉 {$member->name} ({$audienceId}) declared as Lucky Draw Winner for Day {$day}!");
    }

    // ── Delete audience member ────────────────────────────────────────────────
    public function destroy(Audience $audience)
    {
        $audience->delete();
        return redirect()->route('admin-audience')->with('success', 'Record deleted.');
    }
}