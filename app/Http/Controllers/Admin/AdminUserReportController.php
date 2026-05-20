<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Season;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminUserReportController extends Controller
{
    private array $ageCategories = ['U-11', 'U-13', 'U-15', 'U-19'];

    // ── Hub ────────────────────────────────────────────────────────────────────

    public function index()
    {
        $totalPlayers   = DB::table('players')->count();
        $totalPaid      = DB::table('players')->where('payment_status', 'paid')->count();
        $totalPending   = DB::table('players')->where('payment_status', 'pending')->count();

        $paidSingles    = DB::table('players')->where('payment_status', 'paid')->where('mode', 'singles')->count();
        $paidDoubles    = DB::table('players')->where('payment_status', 'paid')->where('mode', 'doubles')->count();
        $pendingSingles = DB::table('players')->where('payment_status', 'pending')->where('mode', 'singles')->count();
        $pendingDoubles = DB::table('players')->where('payment_status', 'pending')->where('mode', 'doubles')->count();

        return view('Admin.users.index', compact(
            'totalPlayers',
            'totalPaid', 'totalPending',
            'paidSingles', 'paidDoubles',
            'pendingSingles', 'pendingDoubles'
        ));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  EDIT / UPDATE / DESTROY
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Show the edit form for a player.
     * Accessible from any listing page via the edit button.
     */
    public function edit(int $id)
    {
        $player = Player::with(['state', 'city'])->findOrFail($id);
        $states = State::orderBy('name')->get();
        $cities = City::when($player->state_id, fn($q) => $q->where('state_id', $player->state_id))
                      ->orderBy('name')
                      ->get();

        return view('Admin.users.edit', compact('player', 'states', 'cities'));
    }

    /**
     * Persist changes to a player.
     * player_id and season_id are intentionally excluded from $fillable below.
     */
    public function update(Request $request, int $id)
    {
        $player = Player::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'gender'         => 'required|in:male,female,other',
            'age'            => 'required|in:U-11,U-13,U-15,U-19',
            'sport'          => 'required|string|max:100',
            'tshirt_size'    => 'required|in:XS,S,M,L,XL,XXL',
            'mode'           => 'required|in:singles,doubles',
            'payment_status' => 'required|in:paid,pending',
            'state_id'       => 'nullable|exists:states,id',
            'city_id'        => 'nullable|exists:cities,id',
            'address'        => 'nullable|string|max:500',
            'profile_photo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aadhar_proof'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // ── Handle profile photo upload ────────────────────────────────────
        if ($request->hasFile('profile_photo')) {
            if ($player->profile_photo) {
                Storage::disk('public')->delete($player->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        // ── Handle aadhar proof upload ─────────────────────────────────────
        if ($request->hasFile('aadhar_proof')) {
            if ($player->aadhar_proof) {
                Storage::disk('public')->delete($player->aadhar_proof);
            }
            $validated['aadhar_proof'] = $request->file('aadhar_proof')
                ->store('aadhar_proofs', 'public');
        }

        // player_id and season_id are never in $validated — they are safe
        $player->update($validated);

        return redirect()->back()->with('success', "Player #{$player->player_id} updated successfully.");
    }

    /**
     * Delete a player and their uploaded files.
     */
    public function destroy(int $id)
    {
        $player = Player::findOrFail($id);

        if ($player->profile_photo) {
            Storage::disk('public')->delete($player->profile_photo);
        }
        if ($player->aadhar_proof) {
            Storage::disk('public')->delete($player->aadhar_proof);
        }

        $player->delete();

        return redirect()->route('admin-users.index')
                         ->with('success', 'Player deleted successfully.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PAID FLOW
    // ══════════════════════════════════════════════════════════════════════════

    public function paidTournaments()
    {
        $seasons = Season::orderByDesc('created_at')->get()->map(function ($season) {
            $season->singles_count = DB::table('players')->where('payment_status', 'paid')->where('mode', 'singles')->count();
            $season->doubles_count = DB::table('players')->where('payment_status', 'paid')->where('mode', 'doubles')->count();
            $season->total_count   = $season->singles_count + $season->doubles_count;
            return $season;
        });
        return view('Admin.users.paid.tournaments', compact('seasons'));
    }

    public function paidCategories($seasonId)
    {
        $season       = Season::findOrFail($seasonId);
        $singlesCount = DB::table('players')->where('payment_status', 'paid')->where('mode', 'singles')->count();
        $doublesCount = DB::table('players')->where('payment_status', 'paid')->where('mode', 'doubles')->count();
        return view('Admin.users.paid.categories', compact('season', 'singlesCount', 'doublesCount'));
    }

    public function paidSinglesAge($seasonId)
    {
        $season        = Season::findOrFail($seasonId);
        $ageCategories = $this->ageCategories;
        $ageCounts     = [];
        foreach ($ageCategories as $age) {
            $ageCounts[$age] = DB::table('players')
                ->where('payment_status', 'paid')
                ->where('mode', 'singles')
                ->where('age', $age)
                ->count();
        }
        return view('Admin.users.paid.single_age', compact('season', 'ageCategories', 'ageCounts'));
    }

    public function paidDoublesAge($seasonId)
    {
        $season        = Season::findOrFail($seasonId);
        $ageCategories = $this->ageCategories;
        $ageCounts     = [];
        foreach ($ageCategories as $age) {
            $ageCounts[$age] = DB::table('players')
                ->where('payment_status', 'paid')
                ->where('mode', 'doubles')
                ->where('age', $age)
                ->count();
        }
        return view('Admin.users.paid.doubles_age', compact('season', 'ageCategories', 'ageCounts'));
    }

    public function paidSingles($seasonId, $age)
    {
        $season  = Season::findOrFail($seasonId);
        $age     = urldecode($age);
        
        // Changed ->get() to ->paginate(15) to make the dataset sliceable
        $players = DB::table('players as p')
            ->leftJoin('payments_data as pay', function ($join) {
                $join->on('pay.season_id', '=', 'p.season_id')
                     ->where('pay.registration_type', '=', 'single')
                     ->where('pay.status', '=', 'paid');
            })
            ->leftJoin('states as s', 's.id', '=', 'p.state_id')
            ->where('p.payment_status', 'paid')
            ->where('p.mode', 'singles')
            ->where('p.age', $age)
            ->select('p.id','p.player_id','p.season_id','p.name','p.email','p.phone',
                     'p.gender','p.age','p.sport','p.tshirt_size',
                     'p.mode','p.payment_status','p.created_at',
                     'p.profile_photo','p.aadhar_proof',
                     's.name as state_name','pay.amount')
            ->orderByDesc('p.created_at')
            ->paginate(15); 

        // Total count matches the absolute collection total
        $count = $players->total();
        
        return view('Admin.users.paid.singles', compact('season', 'players', 'count', 'age'));
    }

    public function paidDoubles($seasonId, $age)
    {
        $season  = Season::findOrFail($seasonId);
        $age     = urldecode($age);
        $players = DB::table('players as p')
            ->leftJoin('states as s', 's.id', '=', 'p.state_id')
            ->where('p.payment_status', 'paid')
            ->where('p.mode', 'doubles')
            ->where('p.age', $age)
            ->select('p.id','p.player_id','p.season_id','p.name','p.email',
                     'p.phone','p.gender','p.age','p.sport','p.tshirt_size',
                     'p.payment_status','p.created_at','s.name as state_name',
                     'p.profile_photo','p.aadhar_proof')
            ->orderBy('p.season_id')->orderByDesc('p.created_at')->get();

        $pairs = $players->groupBy('season_id')->map(function ($group) {
            $p1      = $group->first();
            $p2      = $group->count() > 1 ? $group->last() : null;
            $payment = DB::table('payments_data')
                ->where('season_id', $p1->season_id)
                ->where('registration_type', 'double')
                ->where('status', 'paid')->first();
            return ['season_id' => $p1->season_id, 'player1' => $p1, 'player2' => $p2, 'payment' => $payment];
        })->values();

        $count = $pairs->count();
        return view('Admin.users.paid.doubles', compact('season', 'pairs', 'count', 'age'));
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  NOT-PAID FLOW
    // ══════════════════════════════════════════════════════════════════════════

    public function notPaidTournaments()
    {
        $seasons = Season::orderByDesc('created_at')->get()->map(function ($season) {
            $season->singles_count = DB::table('players')->where('payment_status', 'pending')->where('mode', 'singles')->count();
            $season->doubles_count = DB::table('players')->where('payment_status', 'pending')->where('mode', 'doubles')->count();
            $season->total_count   = $season->singles_count + $season->doubles_count;
            return $season;
        });
        return view('Admin.users.notpaid.tournaments', compact('seasons'));
    }

    public function notPaidCategories($seasonId)
    {
        $season       = Season::findOrFail($seasonId);
        $singlesCount = DB::table('players')->where('payment_status', 'pending')->where('mode', 'singles')->count();
        $doublesCount = DB::table('players')->where('payment_status', 'pending')->where('mode', 'doubles')->count();
        return view('Admin.users.notpaid.categories', compact('season', 'singlesCount', 'doublesCount'));
    }

    public function notPaidSinglesAge($seasonId)
    {
        $season        = Season::findOrFail($seasonId);
        $ageCategories = $this->ageCategories;
        $ageCounts     = [];
        foreach ($ageCategories as $age) {
            $ageCounts[$age] = DB::table('players')
                ->where('payment_status', 'pending')
                ->where('mode', 'singles')
                ->where('age', $age)
                ->count();
        }
        return view('Admin.users.notpaid.single_age', compact('season', 'ageCategories', 'ageCounts'));
    }

    public function notPaidDoublesAge($seasonId)
    {
        $season        = Season::findOrFail($seasonId);
        $ageCategories = $this->ageCategories;
        $ageCounts     = [];
        foreach ($ageCategories as $age) {
            $ageCounts[$age] = DB::table('players')
                ->where('payment_status', 'pending')
                ->where('mode', 'doubles')
                ->where('age', $age)
                ->count();
        }
        return view('Admin.users.notpaid.doubles_age', compact('season', 'ageCategories', 'ageCounts'));
    }

    public function notPaidSingles($seasonId, $age)
    {
        $season  = Season::findOrFail($seasonId);
        $age     = urldecode($age);
        $players = DB::table('players as p')
            ->leftJoin('states as s', 's.id', '=', 'p.state_id')
            ->where('p.payment_status', 'pending')
            ->where('p.mode', 'singles')
            ->where('p.age', $age)
            ->select('p.id','p.player_id','p.season_id','p.name','p.email','p.phone',
                     'p.gender','p.age','p.sport','p.tshirt_size',
                     'p.mode','p.payment_status','p.created_at',
                     's.name as state_name','p.profile_photo','p.aadhar_proof')
            ->orderByDesc('p.created_at')->get();
        $count = $players->count();
        return view('Admin.users.notpaid.singles', compact('season', 'players', 'count', 'age'));
    }

    public function notPaidDoubles($seasonId, $age)
    {
        $season  = Season::findOrFail($seasonId);
        $age     = urldecode($age);
        $players = DB::table('players as p')
            ->leftJoin('states as s', 's.id', '=', 'p.state_id')
            ->where('p.payment_status', 'pending')
            ->where('p.mode', 'doubles')
            ->where('p.age', $age)
            ->select('p.id','p.player_id','p.season_id','p.name','p.email',
                     'p.phone','p.gender','p.age','p.sport','p.tshirt_size',
                     'p.payment_status','p.created_at',
                     's.name as state_name','p.profile_photo','p.aadhar_proof')
            ->orderBy('p.season_id')->orderByDesc('p.created_at')->get();

        $pairs = $players->groupBy('season_id')->map(function ($group) {
            $p1 = $group->first();
            $p2 = $group->count() > 1 ? $group->last() : null;
            return ['season_id' => $p1->season_id, 'player1' => $p1, 'player2' => $p2];
        })->values();

        $count = $pairs->count();
        return view('Admin.users.notpaid.doubles', compact('season', 'pairs', 'count', 'age'));
    }


    public function editPair(string $seasonId)
{
    $players = Player::with('state')
        ->where('season_id', $seasonId)
        ->where('mode', 'doubles')
        ->get();

    if ($players->count() !== 2) {
        return redirect()->back()->with('error', 'Doubles pair not found.');
    }

    $p1     = $players->first();
    $p2     = $players->last();
    $states = State::orderBy('name')->get();

    return view('Admin.users.edit-pair', compact('p1', 'p2', 'states', 'seasonId'));
}

public function updatePair(Request $request, string $seasonId)
{
    $players = Player::where('season_id', $seasonId)
        ->where('mode', 'doubles')
        ->get();

    if ($players->count() !== 2) {
        return redirect()->back()->with('error', 'Doubles pair not found.');
    }

    $p1 = $players->first();
    $p2 = $players->last();

    $request->validate([
        'p1_name'          => 'required|string|max:255',
        'p1_phone'         => 'required|string|max:20',
        'p1_gender'        => 'required|in:Male,Female',
        'p1_age'           => 'required|in:U-11,U-13,U-15,U-19',
        'p1_tshirt_size'   => 'required|in:XS,S,M,L,XL,XXL',
        'p1_state_id'      => 'nullable|exists:states,id',
        'p1_address'       => 'nullable|string|max:500',
        'p1_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'p1_aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',

        'p2_name'          => 'required|string|max:255',
        'p2_phone'         => 'required|string|max:20',
        'p2_gender'        => 'required|in:Male,Female',
        'p2_age'           => 'required|in:U-11,U-13,U-15,U-19',
        'p2_tshirt_size'   => 'required|in:XS,S,M,L,XL,XXL',
        'p2_state_id'      => 'nullable|exists:states,id',
        'p2_address'       => 'nullable|string|max:500',
        'p2_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'p2_aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
    ]);

    if ($request->p1_age !== $request->p2_age) {
        return back()->withErrors(['p2_age' => 'Both players must be in the same age category.'])->withInput();
    }
      $paymentStatus = $request->payment_status ?? $p1->payment_status;

    // Update Player 1
    $p1Data = [
        'name'           => $request->p1_name,
        'phone'          => $request->p1_phone,
        'gender'         => $request->p1_gender,
        'age'            => $request->p1_age,
        'tshirt_size'    => $request->p1_tshirt_size,
        'state_id'       => $request->p1_state_id,
        'address'        => $request->p1_address,
        'payment_status' => $paymentStatus,
        // 'payment_status' => $request->p1_payment_status ?? $p1->payment_status,
    ];
    if ($request->hasFile('p1_profile_photo')) {
        if ($p1->profile_photo) Storage::disk('public')->delete($p1->profile_photo);
        $p1Data['profile_photo'] = $request->file('p1_profile_photo')->store('profile_photos', 'public');
    }
    if ($request->hasFile('p1_aadhar_proof')) {
        if ($p1->aadhar_proof) Storage::disk('public')->delete($p1->aadhar_proof);
        $p1Data['aadhar_proof'] = $request->file('p1_aadhar_proof')->store('aadhar_proofs', 'public');
    }
    $p1->update($p1Data);

    // Update Player 2
    $p2Data = [
        'name'           => $request->p2_name,
        'phone'          => $request->p2_phone,
        'gender'         => $request->p2_gender,
        'age'            => $request->p2_age,
        'tshirt_size'    => $request->p2_tshirt_size,
        'state_id'       => $request->p2_state_id,
        'address'        => $request->p2_address,
        'payment_status' => $paymentStatus,
        // 'payment_status' => $request->p2_payment_status ?? $p2->payment_status,
    ];
    if ($request->hasFile('p2_profile_photo')) {
        if ($p2->profile_photo) Storage::disk('public')->delete($p2->profile_photo);
        $p2Data['profile_photo'] = $request->file('p2_profile_photo')->store('profile_photos', 'public');
    }
    if ($request->hasFile('p2_aadhar_proof')) {
        if ($p2->aadhar_proof) Storage::disk('public')->delete($p2->aadhar_proof);
        $p2Data['aadhar_proof'] = $request->file('p2_aadhar_proof')->store('aadhar_proofs', 'public');
    }
    $p2->update($p2Data);

    return redirect()->back()->with('success', "Doubles pair {$seasonId} updated successfully.");
}
}