<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use App\Models\Tournament;
use Illuminate\Http\Request;

class AudienceRegistrationController extends Controller
{
    /**
     * Show the public registration form.
     */
    public function show(Request $request)
    {
        $tournaments = Tournament::with('seasons')->get();
        $seasonId = $request->get('tournament_season_id');

        return view('Admin.audience.audience_register', compact('tournaments', 'seasonId'));
    }

    /**
     * Handle public registration submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:15',
            'city' => 'required|string|max:100',
            'age' => 'required|integer|min:1|max:120',
            'tournament_season_id' => 'required|exists:tournament_seasons,id',
        ]);

        $audience = Audience::create($request->only(
            'name',
            'email',
            'phone',
            'city',
            'age',
            'tournament_season_id'
        ));

        return redirect()->route('audience.register')
            ->with('success', "You're registered! Your Audience ID is: {$audience->audience_id}");
    }
}