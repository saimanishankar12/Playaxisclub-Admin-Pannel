<?php

namespace App\Http\Controllers\Admin;
use App\Models\Player;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function singles()
{
    $players = Player::where('mode', 'singles')->get();
    return view('Admin.players.singles', compact('players'));
}

public function doubles()
{
    $players = Player::where('mode', 'doubles')->get();
    return view('Admin.players.doubles', compact('players'));
}



}
