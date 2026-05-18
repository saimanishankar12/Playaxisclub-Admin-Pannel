<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\Tournament;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    // ── List all sponsors (filterable by tournament + season) ─────────────────
    public function index(Request $request)
    {
        $tournaments      = Tournament::with('seasons')->get();
        $tournamentId     = $request->get('tournament_id');
        $seasonId         = $request->get('tournament_season_id');

        $sponsors = Sponsor::with(['tournament', 'season'])
            ->when($tournamentId, fn($q) => $q->where('tournament_id', $tournamentId))
            ->when($seasonId,     fn($q) => $q->where('tournament_season_id', $seasonId))
            ->orderByDesc('created_at')
            ->get();

        $totalSponsorship = $sponsors->sum('package');

        return view('Admin.sponsors.index', compact(
            'tournaments', 'tournamentId', 'seasonId',
            'sponsors', 'totalSponsorship'
        ));
    }

    // ── Add new sponsor ───────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'package'                => 'required|in:25000,50000,75000,100000',
            'tournament_id'          => 'required|exists:tournaments,id',
            'tournament_season_id'   => 'required|exists:tournament_seasons,id',
            'notes'                  => 'nullable|string',
        ]);

        Sponsor::create($request->only(
            'name', 'package', 'tournament_id', 'tournament_season_id', 'notes'
        ));

        return redirect()->route('admin-sponsors')
            ->with('success', 'Sponsor added successfully!');
    }




    public function update(Request $request, Sponsor $sponsor)
{
    $request->validate([
        'name'                 => 'required|string|max:255',
        'package'              => 'required|numeric',
        'tournament_id'        => 'required|exists:tournaments,id',
        'tournament_season_id' => 'required|exists:tournament_seasons,id',
        'notes'                => 'nullable|string',
    ]);

    $sponsor->update($request->only('name', 'package', 'tournament_id', 'tournament_season_id', 'notes'));

    return redirect()->route('admin-sponsors')->with('success', 'Sponsor updated successfully.');
}

    // ── Delete sponsor ────────────────────────────────────────────────────────
    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        return redirect()->route('admin-sponsors')
            ->with('success', 'Sponsor removed.');
    }
}
