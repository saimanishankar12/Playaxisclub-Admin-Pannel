<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\DoublesPair;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Log;

class TournamentController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    const SEASON_PREFIX = 'ALK';

    // ── Sheet tab names ────────────────────────────────────────────────────────
    const SHEET_NOT_PAID_SINGLES = 'Not-paid-singles';
    const SHEET_NOT_PAID_DOUBLES = 'Not-paid-doubles';
    const SHEET_PAID_SINGLES     = 'Paid-Singles';
    const SHEET_PAID_DOUBLES     = 'Paid-Doubles';

    /* ─────────────────────────────
     |  SINGLES REGISTRATION
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

        $phonePaid = Player::where('phone', $request->phone)
            ->where('payment_status', 'paid')
            ->where('mode', 'singles')
            ->exists();

        $emailPaid = Player::where('email', $request->email)
            ->where('payment_status', 'paid')
            ->where('mode', 'singles')
            ->exists();

        if ($phonePaid || $emailPaid) {
            return response()->json([
                'message' => 'You have already completed registration.',
                'status'  => 'already_paid',
            ], 409);
        }

        $player = DB::transaction(function () use ($request) {

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
                'payment_status' => 'pending',
            ]);

            DB::table('singles')->insert([
                'player_id'  => $player->id,
                'season_id'  => $player->season_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $player;
        });

        $stateName = DB::table('states')->where('id', $player->state_id)->value('name');

        // ── Append to Not-paid-singles sheet ───────────────────────────────────
        try {
            $this->sheetService->append(
                $this->buildNotPaidSinglesRow($player, $stateName),
                self::SHEET_NOT_PAID_SINGLES
            );
        } catch (\Throwable $e) {
            Log::error('Google Sheet Error (Not-Paid Singles)', ['message' => $e->getMessage()]);
        }

        return response()->json([
            'success'   => true,
            'status'    => 'registered',
            'player_id' => $player->player_id,
            'season_id' => $player->season_id,
        ]);
    }

    /* ─────────────────────────────
     |  SINGLES UPDATE
     ───────────────────────────── */
    public function updateSingles(Request $request, string $playerId)
    {
        $player = Player::where('player_id', $playerId)
            ->where('mode', 'singles')
            ->first();

        if (!$player) {
            return response()->json(['message' => 'Player not found.'], 404);
        }

        if ($player->payment_status === 'paid') {
            return response()->json([
                'message' => 'Registration is complete. Details cannot be changed after payment.',
                'status'  => 'already_paid',
            ], 409);
        }

        if ($request->has('email') && $request->email !== $player->email) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['email' => ['Email address cannot be changed after registration.']],
            ], 422);
        }

        if ($request->has('phone') && $request->phone !== $player->phone) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['phone' => ['Phone number cannot be changed after registration.']],
            ], 422);
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'state_id'      => 'required',
            'address'       => 'required|string|max:1000',
            'age'           => 'required|in:U-11,U-13,U-15,U-19',
            'sport'         => 'required',
            'gender'        => 'required|in:Male,Female',
            'tshirt_size'   => 'required',
            'aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $updateData = [
            'name'        => $request->name,
            'state_id'    => $request->state_id,
            'address'     => $request->address,
            'age'         => $request->age,
            'sport'       => $request->sport,
            'gender'      => $request->gender,
            'tshirt_size' => $request->tshirt_size,
        ];

        if ($request->hasFile('aadhar_proof')) {
            $updateData['aadhar_proof'] = $request->file('aadhar_proof')
                ->store('aadhar_proofs', 'public');
        }

        if ($request->hasFile('profile_photo')) {
            $updateData['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        $player->update($updateData);

        return response()->json([
            'success'   => true,
            'message'   => 'Details updated successfully.',
            'player_id' => $player->player_id,
            'season_id' => $player->season_id,
        ]);
    }

    /* ─────────────────────────────
     |  DOUBLES REGISTRATION
     ───────────────────────────── */
    public function registerDoubles(Request $request)
    {
        $request->validate([
            'player1.name'          => 'required|string|max:255',
            'player1.email'         => 'required|email|max:255',
            'player1.phone'         => ['required', 'regex:/^(\+91)?[6-9]\d{9}$/'],
            'player1.state_id'      => 'required',
            'player1.address'       => 'required|string|max:1000',
            'player1.age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player1.sport'         => 'required',
            'player1.gender'        => 'required|in:Male,Female',
            'player1.tshirt_size'   => 'required',
            'player1.aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player1.profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

            'player2.name'          => 'required|string|max:255',
            'player2.email'         => 'required|email|max:255',
            'player2.phone'         => ['required', 'regex:/^(\+91)?[6-9]\d{9}$/'],
            'player2.state_id'      => 'required',
            'player2.address'       => 'required|string|max:1000',
            'player2.age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player2.sport'         => 'required',
            'player2.gender'        => 'required|in:Male,Female',
            'player2.tshirt_size'   => 'required',
            'player2.aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player2.profile_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->input('player1.age') !== $request->input('player2.age')) {
            return response()->json([
                'message' => 'Both players must be in the same age category.',
                'errors'  => [
                    'player2.age' => [
                        'Age category must match Player 1 (' . $request->input('player1.age') . ').'
                    ],
                ],
            ], 422);
        }

        if ($request->input('player1.email') === $request->input('player2.email')) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['player2.email' => ['Player 2 email must be different from Player 1.']],
            ], 422);
        }

        if ($request->input('player1.phone') === $request->input('player2.phone')) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['player2.phone' => ['Player 2 phone must be different from Player 1.']],
            ], 422);
        }

        $p1AlreadyPaid = Player::where(function ($q) use ($request) {
                $q->where('phone', $request->input('player1.phone'))
                  ->orWhere('email', $request->input('player1.email'));
            })
            ->where('payment_status', 'paid')
            ->where('mode', 'doubles')
            ->exists();

        $p2AlreadyPaid = Player::where(function ($q) use ($request) {
                $q->where('phone', $request->input('player2.phone'))
                  ->orWhere('email', $request->input('player2.email'));
            })
            ->where('payment_status', 'paid')
            ->where('mode', 'doubles')
            ->exists();

        if ($p1AlreadyPaid) {
            return response()->json([
                'message' => 'Player 1 is already registered in a doubles tournament.',
                'status'  => 'already_paid',
                'errors'  => [
                    'player1.email' => ['Player 1 is already registered in doubles.'],
                    'player1.phone' => ['Player 1 is already registered in doubles.'],
                ],
            ], 409);
        }

        if ($p2AlreadyPaid) {
            return response()->json([
                'message' => 'Player 2 is already registered in a doubles tournament.',
                'status'  => 'already_paid',
                'errors'  => [
                    'player2.email' => ['Player 2 is already registered in doubles.'],
                    'player2.phone' => ['Player 2 is already registered in doubles.'],
                ],
            ], 409);
        }

        $data = DB::transaction(function () use ($request) {

            $seasonId = $this->generateDoublesSeasonId();

            $existingP1 = Player::where(function ($q) use ($request) {
                    $q->where('phone', $request->input('player1.phone'))
                      ->orWhere('email', $request->input('player1.email'));
                })
                ->where('mode', 'doubles')
                ->where('payment_status', 'pending')
                ->first();

            $p1Aadhar = $request->hasFile('player1.aadhar_proof')
                ? $request->file('player1.aadhar_proof')->store('aadhar_proofs', 'public')
                : ($existingP1->aadhar_proof ?? null);

            $p1Photo = $request->hasFile('player1.profile_photo')
                ? $request->file('player1.profile_photo')->store('profile_photos', 'public')
                : ($existingP1->profile_photo ?? null);

            if ($existingP1) {
                $existingP1->update([
                    'season_id'     => $seasonId,
                    'name'          => $request->input('player1.name'),
                    'state_id'      => $request->input('player1.state_id'),
                    'address'       => $request->input('player1.address'),
                    'age'           => $request->input('player1.age'),
                    'sport'         => $request->input('player1.sport'),
                    'gender'        => $request->input('player1.gender'),
                    'tshirt_size'   => $request->input('player1.tshirt_size'),
                    'aadhar_proof'  => $p1Aadhar,
                    'profile_photo' => $p1Photo,
                ]);
                $p1 = $existingP1;
            } else {
                $p1 = Player::create([
                    'player_id'      => $this->generatePlayerId(),
                    'season_id'      => $seasonId,
                    'name'           => $request->input('player1.name'),
                    'email'          => $request->input('player1.email'),
                    'phone'          => $request->input('player1.phone'),
                    'state_id'       => $request->input('player1.state_id'),
                    'address'        => $request->input('player1.address'),
                    'age'            => $request->input('player1.age'),
                    'sport'          => $request->input('player1.sport'),
                    'gender'         => $request->input('player1.gender'),
                    'tshirt_size'    => $request->input('player1.tshirt_size'),
                    'aadhar_proof'   => $p1Aadhar,
                    'profile_photo'  => $p1Photo,
                    'mode'           => 'doubles',
                    'payment_status' => 'pending',
                ]);
            }

            $existingP2 = Player::where(function ($q) use ($request) {
                    $q->where('phone', $request->input('player2.phone'))
                      ->orWhere('email', $request->input('player2.email'));
                })
                ->where('mode', 'doubles')
                ->where('payment_status', 'pending')
                ->first();

            $p2Aadhar = $request->hasFile('player2.aadhar_proof')
                ? $request->file('player2.aadhar_proof')->store('aadhar_proofs', 'public')
                : ($existingP2->aadhar_proof ?? null);

            $p2Photo = $request->hasFile('player2.profile_photo')
                ? $request->file('player2.profile_photo')->store('profile_photos', 'public')
                : ($existingP2->profile_photo ?? null);

            if ($existingP2) {
                $existingP2->update([
                    'season_id'     => $seasonId,
                    'name'          => $request->input('player2.name'),
                    'state_id'      => $request->input('player2.state_id'),
                    'address'       => $request->input('player2.address'),
                    'age'           => $request->input('player2.age'),
                    'sport'         => $request->input('player2.sport'),
                    'gender'        => $request->input('player2.gender'),
                    'tshirt_size'   => $request->input('player2.tshirt_size'),
                    'aadhar_proof'  => $p2Aadhar,
                    'profile_photo' => $p2Photo,
                ]);
                $p2 = $existingP2;
            } else {
                $p2 = Player::create([
                    'player_id'      => $this->generatePlayerId(),
                    'season_id'      => $seasonId,
                    'name'           => $request->input('player2.name'),
                    'email'          => $request->input('player2.email'),
                    'phone'          => $request->input('player2.phone'),
                    'state_id'       => $request->input('player2.state_id'),
                    'address'        => $request->input('player2.address'),
                    'age'            => $request->input('player2.age'),
                    'sport'          => $request->input('player2.sport'),
                    'gender'         => $request->input('player2.gender'),
                    'tshirt_size'    => $request->input('player2.tshirt_size'),
                    'aadhar_proof'   => $p2Aadhar,
                    'profile_photo'  => $p2Photo,
                    'mode'           => 'doubles',
                    'payment_status' => 'pending',
                ]);
            }

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

        $p1State = DB::table('states')->where('id', $p1->state_id)->value('name');
        $p2State = DB::table('states')->where('id', $p2->state_id)->value('name');

        // ── Append to Not-paid-doubles sheet ───────────────────────────────────
        try {
            $this->sheetService->append(
                $this->buildNotPaidDoublesRow($p1, $p1State, $p2, $p2State),
                self::SHEET_NOT_PAID_DOUBLES
            );
        } catch (\Throwable $e) {
            Log::error('Google Sheet Error (Not-Paid Doubles)', ['message' => $e->getMessage()]);
        }

        return response()->json([
            'success'    => true,
            'status'     => 'registered',
            'player1_id' => $p1->player_id,
            'player2_id' => $p2->player_id,
            'season_id'  => $seasonId,
        ]);
    }

    /* ─────────────────────────────
     |  DOUBLES UPDATE
     ───────────────────────────── */
    public function updateDoubles(Request $request, string $seasonId)
    {
        $players = Player::where('season_id', $seasonId)
            ->where('mode', 'doubles')
            ->get();

        if ($players->count() !== 2) {
            return response()->json(['message' => 'Doubles pair not found.'], 404);
        }

        $p1 = $players->firstWhere('player_id', $request->input('player1.player_id'));
        $p2 = $players->firstWhere('player_id', $request->input('player2.player_id'));

        if (!$p1 || !$p2) {
            return response()->json(['message' => 'Player IDs do not match this doubles pair.'], 422);
        }

        if ($p1->payment_status === 'paid' || $p2->payment_status === 'paid') {
            return response()->json([
                'message' => 'Registration is complete. Details cannot be changed after payment.',
                'status'  => 'already_paid',
            ], 409);
        }

        if ($request->has('player1.email') && $request->input('player1.email') !== $p1->email) {
            return response()->json(['message' => 'Validation failed.', 'errors' => ['player1.email' => ['Player 1 email cannot be changed.']]], 422);
        }
        if ($request->has('player1.phone') && $request->input('player1.phone') !== $p1->phone) {
            return response()->json(['message' => 'Validation failed.', 'errors' => ['player1.phone' => ['Player 1 phone cannot be changed.']]], 422);
        }
        if ($request->has('player2.email') && $request->input('player2.email') !== $p2->email) {
            return response()->json(['message' => 'Validation failed.', 'errors' => ['player2.email' => ['Player 2 email cannot be changed.']]], 422);
        }
        if ($request->has('player2.phone') && $request->input('player2.phone') !== $p2->phone) {
            return response()->json(['message' => 'Validation failed.', 'errors' => ['player2.phone' => ['Player 2 phone cannot be changed.']]], 422);
        }

        $request->validate([
            'player1.player_id'     => 'required|string',
            'player1.name'          => 'required|string|max:255',
            'player1.state_id'      => 'required',
            'player1.address'       => 'required|string|max:1000',
            'player1.age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player1.sport'         => 'required',
            'player1.gender'        => 'required|in:Male,Female',
            'player1.tshirt_size'   => 'required',
            'player1.aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player1.profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'player2.player_id'     => 'required|string',
            'player2.name'          => 'required|string|max:255',
            'player2.state_id'      => 'required',
            'player2.address'       => 'required|string|max:1000',
            'player2.age'           => 'required|in:U-11,U-13,U-15,U-19',
            'player2.sport'         => 'required',
            'player2.gender'        => 'required|in:Male,Female',
            'player2.tshirt_size'   => 'required',
            'player2.aadhar_proof'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'player2.profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->input('player1.age') !== $request->input('player2.age')) {
            return response()->json([
                'message' => 'Both players must be in the same age category.',
                'errors'  => ['player2.age' => ['Age category must match Player 1.']],
            ], 422);
        }

        $p1Update = [
            'name'        => $request->input('player1.name'),
            'state_id'    => $request->input('player1.state_id'),
            'address'     => $request->input('player1.address'),
            'age'         => $request->input('player1.age'),
            'sport'       => $request->input('player1.sport'),
            'gender'      => $request->input('player1.gender'),
            'tshirt_size' => $request->input('player1.tshirt_size'),
        ];
        if ($request->hasFile('player1.aadhar_proof')) {
            $p1Update['aadhar_proof'] = $request->file('player1.aadhar_proof')->store('aadhar_proofs', 'public');
        }
        if ($request->hasFile('player1.profile_photo')) {
            $p1Update['profile_photo'] = $request->file('player1.profile_photo')->store('profile_photos', 'public');
        }
        $p1->update($p1Update);

        $p2Update = [
            'name'        => $request->input('player2.name'),
            'state_id'    => $request->input('player2.state_id'),
            'address'     => $request->input('player2.address'),
            'age'         => $request->input('player2.age'),
            'sport'       => $request->input('player2.sport'),
            'gender'      => $request->input('player2.gender'),
            'tshirt_size' => $request->input('player2.tshirt_size'),
        ];
        if ($request->hasFile('player2.aadhar_proof')) {
            $p2Update['aadhar_proof'] = $request->file('player2.aadhar_proof')->store('aadhar_proofs', 'public');
        }
        if ($request->hasFile('player2.profile_photo')) {
            $p2Update['profile_photo'] = $request->file('player2.profile_photo')->store('profile_photos', 'public');
        }
        $p2->update($p2Update);

        return response()->json([
            'success'    => true,
            'message'    => 'Details updated successfully.',
            'player1_id' => $p1->player_id,
            'player2_id' => $p2->player_id,
            'season_id'  => $seasonId,
        ]);
    }

    /* ─────────────────────────────
     |  SHEET ROW BUILDERS
     ───────────────────────────── */

    /**
     * Build a row for the Not-paid-singles sheet.
     */
    public function buildNotPaidSinglesRow(
        Player $p1,
        ?string $p1State
    ): array {
        return [
            $p1->player_id,
            $p1->season_id,
            $p1->name,
            $p1->email,
            $p1->phone,
            $p1State ?? '',
            $p1->address,
            $p1->sport,
            $p1->age,
            $p1->gender,
            $p1->tshirt_size,
            'Pending',
            now()->format('d/m/Y g:i A'),
        ];
    }

    /**
     * Build a row for the Not-paid-doubles sheet.
     */
    public function buildNotPaidDoublesRow(
        Player $p1,
        ?string $p1State,
        Player $p2,
        ?string $p2State
    ): array {
        return [
            $p1->player_id,
            $p2->player_id,
            $p1->season_id,
            $p1->name,
            $p1->email,
            $p1->phone,
            $p1State ?? '',
            $p2->name,
            $p2->email,
            $p2->phone,
            $p2State ?? '',
            $p1->sport,
            $p1->age,
            $p1->gender,
            $p1->tshirt_size,
            $p2->tshirt_size,
            'Pending',
            now()->format('d/m/Y g:i A'),
        ];
    }

    /**
     * Build a row for Paid-Singles sheet.
     */
    public function buildPaidSinglesRow(
        Player $p1,
        ?string $p1State,
        string $paymentId,
        float $amount
    ): array {
        return [
            $p1->player_id,
            $p1->season_id,
            $p1->name,
            $p1->email,
            $p1->phone,
            $p1State ?? '',
            $p1->address,
            $p1->sport,
            $p1->age,
            $p1->gender,
            $p1->tshirt_size,
            '₹' . number_format($amount, 2),
            $paymentId,
            'Paid',
            now()->format('d/m/Y g:i A'),
        ];
    }

    /**
     * Build a row for Paid-Doubles sheet.
     */
    public function buildPaidDoublesRow(
        Player $p1,
        ?string $p1State,
        Player $p2,
        ?string $p2State,
        string $paymentId,
        float $amount
    ): array {
        return [
            $p1->player_id,
            $p2->player_id,
            $p1->season_id,
            $p1->name,
            $p1->email,
            $p1->phone,
            $p1State ?? '',
            $p2->name,
            $p2->email,
            $p2->phone,
            $p2State ?? '',
            $p1->sport,
            $p1->age,
            $p1->gender,
            $p1->tshirt_size,
            $p2->tshirt_size,
            '₹' . number_format($amount, 2),
            $paymentId,
            'Paid',
            now()->format('d/m/Y g:i A'),
        ];
    }

    /* ─── Helpers ─────────────────────────────────────────────────────────── */

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
        $last = DB::table('singles')->orderByDesc('id')->lockForUpdate()->value('season_id');
        $num  = $last ? ((int) filter_var($last, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return self::SEASON_PREFIX . str_pad($num, 4, '0', STR_PAD_LEFT) . 'S';
    }

    private function generateDoublesSeasonId(): string
    {
        $last = DB::table('doubles')->orderByDesc('id')->lockForUpdate()->value('season_id');
        $num  = $last ? ((int) filter_var($last, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return self::SEASON_PREFIX . str_pad($num, 4, '0', STR_PAD_LEFT) . 'D';
    }

    private function generateDoublesId(): string
    {
        $prefix = 'PACD';
        $last = DB::table('doubles_pair')->orderByDesc('id')->lockForUpdate()->value('doubles_id');
        $num  = $last ? ((int) filter_var($last, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}