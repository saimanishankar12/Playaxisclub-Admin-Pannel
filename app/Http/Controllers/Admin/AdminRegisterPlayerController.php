<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\DoublesPair;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRegisterPlayerController extends Controller
{
    const SEASON_PREFIX = 'ALK';

    /* ─────────────────────────────
     |  SHOW FORM
     ───────────────────────────── */
    public function index()
    {
        $states = DB::table('states')->orderBy('name')->get();
        return view('Admin.register-player', compact('states'));
    }

    /* ─────────────────────────────
     |  REGISTER SINGLES
     ───────────────────────────── */
    public function registerSingles(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => ['required', 'regex:/^(\+91)?[6-9]\d{9}$/'],
            'state_id'      => 'required',
            'address'       => 'required|string|max:1000',
            'age'           => 'required|in:U-11,U-13,U-15,U-19',
            'sport'         => 'required',
            'gender'        => 'required|in:Male,Female',
            'tshirt_size'   => 'required',
            'aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Block if already paid in singles
        $alreadyPaid = Player::where(function ($q) use ($request) {
                $q->where('phone', $request->phone)
                  ->orWhere('email', $request->email);
            })
            ->where('payment_status', 'paid')
            ->where('mode', 'singles')
            ->exists();

        if ($alreadyPaid) {
            return back()->withErrors(['email' => 'This player is already registered and paid for singles.'])->withInput();
        }

        $player = DB::transaction(function () use ($request) {

            // If pending record exists, update it
            $existing = Player::where(function ($q) use ($request) {
                    $q->where('phone', $request->phone)
                      ->orWhere('email', $request->email);
                })
                ->where('mode', 'singles')
                ->where('payment_status', 'pending')
                ->first();

            $aadharPath = $request->hasFile('aadhar_proof')
                ? $request->file('aadhar_proof')->store('aadhar_proofs', 'public')
                : ($existing->aadhar_proof ?? null);

            $photoPath = $request->hasFile('profile_photo')
                ? $request->file('profile_photo')->store('profile_photos', 'public')
                : ($existing->profile_photo ?? null);

            if ($existing) {
                $existing->update([
                    'name'          => $request->name,
                    'state_id'      => $request->state_id,
                    'address'       => $request->address,
                    'age'           => $request->age,
                    'sport'         => $request->sport,
                    'gender'        => $request->gender,
                    'tshirt_size'   => $request->tshirt_size,
                    'aadhar_proof'  => $aadharPath,
                    'profile_photo' => $photoPath,
                ]);
                return $existing;
            }

            // Generate IDs continuing from last in DB
            $playerId = $this->generatePlayerId();
            $seasonId = $this->generateSinglesSeasonId();

            $player = Player::create([
                'player_id'      => $playerId,
                'season_id'      => $seasonId,
                'name'           => $request->name,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'state_id'       => $request->state_id,
                'address'        => $request->address,
                'age'            => $request->age,
                'sport'          => $request->sport,
                'gender'         => $request->gender,
                'tshirt_size'    => $request->tshirt_size,
                'aadhar_proof'   => $aadharPath,
                'profile_photo'  => $photoPath,
                'mode'           => 'singles',
                'payment_status' => 'pending',  // Always pending
            ]);

            DB::table('singles')->insert([
                'player_id'  => $player->id,
                'season_id'  => $player->season_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $player;
        });

        // return back()->with('success', "Singles player registered successfully! Player ID: {$player->player_id} | Season ID: {$player->season_id}");
        return redirect()->route('admin-admin-register-player.confirm')->with([
    'mode'   => 'singles',
    'player' => $player,
]);
    }

    /* ─────────────────────────────
     |  REGISTER DOUBLES
     ───────────────────────────── */
    public function registerDoubles(Request $request)
    {
        $request->validate([
            'player1_name'          => 'required|string|max:255',
            'player1_email'         => 'required|email|max:255',
            'player1_phone'         => ['required', 'regex:/^(\+91)?[6-9]\d{9}$/'],
            'player1_state_id'      => 'required',
            'player1_address'       => 'required|string|max:1000',
            'player1_age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player1_sport'         => 'required',
            'player1_gender'        => 'required|in:Male,Female',
            'player1_tshirt_size'   => 'required',
            'player1_aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player1_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

            'player2_name'          => 'required|string|max:255',
            'player2_email'         => 'required|email|max:255',
            'player2_phone'         => ['required', 'regex:/^(\+91)?[6-9]\d{9}$/'],
            'player2_state_id'      => 'required',
            'player2_address'       => 'required|string|max:1000',
            'player2_age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player2_sport'         => 'required',
            'player2_gender'        => 'required|in:Male,Female',
            'player2_tshirt_size'   => 'required',
            'player2_aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player2_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Age must match
        if ($request->player1_age !== $request->player2_age) {
            return back()->withErrors(['player2_age' => 'Both players must be in the same age category.'])->withInput();
        }

        // Email/phone must differ
        if ($request->player1_email === $request->player2_email) {
            return back()->withErrors(['player2_email' => 'Player 2 email must be different from Player 1.'])->withInput();
        }
        if ($request->player1_phone === $request->player2_phone) {
            return back()->withErrors(['player2_phone' => 'Player 2 phone must be different from Player 1.'])->withInput();
        }

        // Block if already paid
        $p1Paid = Player::where(function ($q) use ($request) {
                $q->where('phone', $request->player1_phone)
                  ->orWhere('email', $request->player1_email);
            })->where('payment_status', 'paid')->where('mode', 'doubles')->exists();

        $p2Paid = Player::where(function ($q) use ($request) {
                $q->where('phone', $request->player2_phone)
                  ->orWhere('email', $request->player2_email);
            })->where('payment_status', 'paid')->where('mode', 'doubles')->exists();

        if ($p1Paid) {
            return back()->withErrors(['player1_email' => 'Player 1 is already registered and paid for doubles.'])->withInput();
        }
        if ($p2Paid) {
            return back()->withErrors(['player2_email' => 'Player 2 is already registered and paid for doubles.'])->withInput();
        }

        $data = DB::transaction(function () use ($request) {

            $seasonId = $this->generateDoublesSeasonId();

            // Player 1
            $existingP1 = Player::where(function ($q) use ($request) {
                    $q->where('phone', $request->player1_phone)
                      ->orWhere('email', $request->player1_email);
                })->where('mode', 'doubles')->where('payment_status', 'pending')->first();

            $p1Aadhar = $request->hasFile('player1_aadhar_proof')
                ? $request->file('player1_aadhar_proof')->store('aadhar_proofs', 'public')
                : ($existingP1->aadhar_proof ?? null);

            $p1Photo = $request->hasFile('player1_profile_photo')
                ? $request->file('player1_profile_photo')->store('profile_photos', 'public')
                : ($existingP1->profile_photo ?? null);

            if ($existingP1) {
                $existingP1->update([
                    'season_id'     => $seasonId,
                    'name'          => $request->player1_name,
                    'state_id'      => $request->player1_state_id,
                    'address'       => $request->player1_address,
                    'age'           => $request->player1_age,
                    'sport'         => $request->player1_sport,
                    'gender'        => $request->player1_gender,
                    'tshirt_size'   => $request->player1_tshirt_size,
                    'aadhar_proof'  => $p1Aadhar,
                    'profile_photo' => $p1Photo,
                ]);
                $p1 = $existingP1;
            } else {
                $p1 = Player::create([
                    'player_id'      => $this->generatePlayerId(),
                    'season_id'      => $seasonId,
                    'name'           => $request->player1_name,
                    'email'          => $request->player1_email,
                    'phone'          => $request->player1_phone,
                    'state_id'       => $request->player1_state_id,
                    'address'        => $request->player1_address,
                    'age'            => $request->player1_age,
                    'sport'          => $request->player1_sport,
                    'gender'         => $request->player1_gender,
                    'tshirt_size'    => $request->player1_tshirt_size,
                    'aadhar_proof'   => $p1Aadhar,
                    'profile_photo'  => $p1Photo,
                    'mode'           => 'doubles',
                    'payment_status' => 'pending',
                ]);
            }

            // Player 2
            $existingP2 = Player::where(function ($q) use ($request) {
                    $q->where('phone', $request->player2_phone)
                      ->orWhere('email', $request->player2_email);
                })->where('mode', 'doubles')->where('payment_status', 'pending')->first();

            $p2Aadhar = $request->hasFile('player2_aadhar_proof')
                ? $request->file('player2_aadhar_proof')->store('aadhar_proofs', 'public')
                : ($existingP2->aadhar_proof ?? null);

            $p2Photo = $request->hasFile('player2_profile_photo')
                ? $request->file('player2_profile_photo')->store('profile_photos', 'public')
                : ($existingP2->profile_photo ?? null);

            if ($existingP2) {
                $existingP2->update([
                    'season_id'     => $seasonId,
                    'name'          => $request->player2_name,
                    'state_id'      => $request->player2_state_id,
                    'address'       => $request->player2_address,
                    'age'           => $request->player2_age,
                    'sport'         => $request->player2_sport,
                    'gender'        => $request->player2_gender,
                    'tshirt_size'   => $request->player2_tshirt_size,
                    'aadhar_proof'  => $p2Aadhar,
                    'profile_photo' => $p2Photo,
                ]);
                $p2 = $existingP2;
            } else {
                $p2 = Player::create([
                    'player_id'      => $this->generatePlayerId(),
                    'season_id'      => $seasonId,
                    'name'           => $request->player2_name,
                    'email'          => $request->player2_email,
                    'phone'          => $request->player2_phone,
                    'state_id'       => $request->player2_state_id,
                    'address'        => $request->player2_address,
                    'age'            => $request->player2_age,
                    'sport'          => $request->player2_sport,
                    'gender'         => $request->player2_gender,
                    'tshirt_size'    => $request->player2_tshirt_size,
                    'aadhar_proof'   => $p2Aadhar,
                    'profile_photo'  => $p2Photo,
                    'mode'           => 'doubles',
                    'payment_status' => 'pending',
                ]);
            }

            // Delete old pair records if re-registering
            DB::table('doubles')
                ->where('player1_id', $p1->id)
                ->orWhere('player2_id', $p2->id)
                ->delete();

            DoublesPair::create([
                'season_id'  => $seasonId,
                'player1_id' => $p1->id,
                'player2_id' => $p2->id,
            ]);

            return [$p1, $p2, $seasonId];
        });

        [$p1, $p2, $seasonId] = $data;

        // return back()->with('success', "Doubles pair registered successfully! Player 1 ID: {$p1->player_id} | Player 2 ID: {$p2->player_id} | Season ID: {$seasonId}");
        return redirect()->route('admin-admin-register-player.confirm')->with([
    'mode' => 'doubles',
    'p1'   => $p1,
    'p2'   => $p2,
]);
    }

    /* ─── ID Generators (same logic as TournamentController) ─────────────── */

    private function generatePlayerId(): string
    {
        $prefix = 'PAC';
        $last = DB::table('players')
            ->where('player_id', 'LIKE', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->value('player_id');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

private function generateSinglesSeasonId(): string
{
    $last = DB::table('players')
        ->where('mode', 'singles')
        ->where('season_id', 'LIKE', 'ALK%S')
        ->orderByDesc('id')
        ->lockForUpdate()
        ->value('season_id');

    $num = $last ? ((int) filter_var($last, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
    return self::SEASON_PREFIX . str_pad($num, 4, '0', STR_PAD_LEFT) . 'S';
}

private function generateDoublesSeasonId(): string
{
    $last = DB::table('players')
        ->where('mode', 'doubles')
        ->where('season_id', 'LIKE', 'ALK%D')
        ->orderByDesc('id')
        ->lockForUpdate()
        ->value('season_id');

    $num = $last ? ((int) filter_var($last, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
    return self::SEASON_PREFIX . str_pad($num, 4, '0', STR_PAD_LEFT) . 'D';
}
}

