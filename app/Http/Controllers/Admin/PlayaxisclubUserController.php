<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

/**
 * Shows ALL players registered in PlayAxisClub (the PAC member list).
 * Route: GET /admin/pac-club  →  admin-pac-users
 */
class PlayaxisclubUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $players = Player::when($search, function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('player_id', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orderByDesc('created_at')
                    ->paginate(25);

        return view('Admin.players.all', compact('players', 'search'));
    }
}
