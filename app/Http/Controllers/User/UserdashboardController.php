<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\MatchGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends Controller
{
    /**
     * Main dashboard — stats, live matches, match history.
     */
    public function index()
    {
        $playerId = session('player_id');
        $player   = Player::where('player_id', $playerId)
            ->with(['city', 'state'])
            ->first();

        // ── Match History ─────────────────────────────────────────────────
        $allMatches = MatchGame::where(function ($q) use ($playerId, $player) {
                if ($player && $player->mode === 'doubles') {
                    $sid = $player->season_id;
                    $q->where('player1_id', $sid)
                      ->orWhere('player2_id', $sid);
                } else {
                    $q->where('player1_id', $playerId)
                      ->orWhere('player2_id', $playerId);
                }
            })
            ->whereIn('status', ['completed', 'live', 'in_progress'])
            ->orderByDesc('started_at')
            ->get();

        $matches = $allMatches->map(function ($match) use ($playerId, $player) {
            $isDoubles = $player && $player->mode === 'doubles';
            $mySlotId  = $isDoubles ? $player->season_id : $playerId;
            $isPlayer1 = $match->player1_id === $mySlotId;

            $opponentName = $isPlayer1 ? $match->getPlayer2Name() : $match->getPlayer1Name();

            $myScore  = $isPlayer1 ? $match->score_p1 : $match->score_p2;
            $oppScore = $isPlayer1 ? $match->score_p2 : $match->score_p1;
            $scoreStr = $match->status === 'completed'
                ? "{$myScore} – {$oppScore}"
                : ($match->score_p1 !== null ? "{$match->score_p1} – {$match->score_p2}" : '—');

            $winnerId = $match->declared_winner_id ?? $match->winner_id;
            if ($match->status === 'completed' && $winnerId) {
                $result = ($winnerId === $mySlotId) ? 'win' : 'loss';
            } else {
                $result = $match->status === 'live' ? 'live' : 'pending';
            }

            $setsStr = ($match->sets_won_p1 !== null && $match->sets_won_p2 !== null)
                ? ($isPlayer1
                    ? "{$match->sets_won_p1}–{$match->sets_won_p2}"
                    : "{$match->sets_won_p2}–{$match->sets_won_p1}")
                : '—';

            return (object) [
                'id'           => $match->id,
                'opponent'     => $opponentName,
                'round'        => $match->getRoundLabel(),
                'match_type'   => $match->match_type,
                'division'     => $match->division,
                'court_no'     => $match->court_no,
                'score'        => $scoreStr,
                'sets'         => $setsStr,
                'result'       => $result,
                'status'       => $match->status,
                'played_at'    => $match->completed_at ?? $match->started_at,
                'started_at'   => $match->started_at,
                'completed_at' => $match->completed_at,
            ];
        });

        // ── Stats ─────────────────────────────────────────────────────────
        $matchesPlayed = $matches->whereIn('result', ['win', 'loss'])->count();
        $wins          = $matches->where('result', 'win')->count();
        $losses        = $matches->where('result', 'loss')->count();

        // ── Live Matches ──────────────────────────────────────────────────
        $liveMatches = MatchGame::whereIn('status', ['live', 'in_progress'])
            ->orderByDesc('started_at')
            ->get();

        // ── Tournament Winners ────────────────────────────────────────────
        $winnerMatches = MatchGame::where('status', 'completed')
            ->whereNotNull('winner_id')
            ->where('round', 'final')
            ->get()
            ->map(function ($m) {
                $m->winner_name = $m->winner_id === $m->player1_id
                    ? $m->getPlayer1Name()
                    : $m->getPlayer2Name();
                return $m;
            });

        return view('user.dashboard', compact(
            'player',
            'matches',
            'matchesPlayed',
            'wins',
            'losses',
            'liveMatches',
            'winnerMatches'
        ));
    }

    /**
     * Show profile view.
     */
    public function showProfile()
    {
        $player = Player::where('player_id', session('player_id'))->first();
        return view('user.profile', compact('player'));
    }

    /**
     * Update editable profile fields.
     */
    public function updateProfile(Request $request)
    {
        $player = Player::where('player_id', session('player_id'))->firstOrFail();

        $request->validate([
            'phone'         => ['nullable', 'string', 'max:15'],
            'email'         => ['nullable', 'email', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'aadhar_proof'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $data = [];

        if ($request->filled('phone')) {
            $data['phone'] = $request->phone;
        }

        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }

        if ($request->hasFile('profile_photo')) {
            if ($player->profile_photo) {
                Storage::disk('public')->delete($player->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('players/profile_photos', 'public');
        }

        if ($request->hasFile('aadhar_proof')) {
            if ($player->aadhar_proof) {
                Storage::disk('public')->delete($player->aadhar_proof);
            }
            $data['aadhar_proof'] = $request->file('aadhar_proof')
                ->store('players/aadhar_proofs', 'public');
        }

        if (!empty($data)) {
            $player->update($data);
        }

        return redirect()->route('user-profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Live scores AJAX endpoint.
     */
    public function liveScores()
    {
        $liveMatches = MatchGame::whereIn('status', ['live', 'in_progress'])
            ->orderByDesc('started_at')
            ->get();

        if ($liveMatches->isEmpty()) {
            return response()->json(['count' => 0, 'matches' => []]);
        }

        $getPlayerInfo = function (?string $slotId, string $matchType): array {
            if (!$slotId) {
                return ['season_id' => '—', 'name' => '—', 'city' => ''];
            }

            if ($matchType === 'singles') {
                $p = \Illuminate\Support\Facades\DB::table('players')
                    ->where('player_id', $slotId)
                    ->first();

                return [
                    'season_id' => $p->season_id ?? $slotId,
                    'name'      => $p->name      ?? '—',
                    'city'      => $p->address   ?? '',
                ];
            }

            $players = \Illuminate\Support\Facades\DB::table('players')
                ->where('season_id', $slotId)
                ->where('mode', 'doubles')
                ->get();

            return [
                'season_id' => $slotId,
                'name'      => $players->pluck('name')->implode(' & ') ?: '—',
                'city'      => $players->pluck('address')->filter()->implode(' / '),
            ];
        };

        $getRoundLabel = function (string $round): string {
            return match($round) {
                'quarter_final' => 'Knock Out',
                'semi_final'    => 'Semi Final',
                'final'         => 'Final',
                default         => ucfirst($round),
            };
        };

        $matches = $liveMatches->map(function ($lm) use ($getPlayerInfo, $getRoundLabel) {
            $p1 = $getPlayerInfo($lm->player1_id, $lm->match_type);
            $p2 = $getPlayerInfo($lm->player2_id, $lm->match_type);

            return [
                'id'           => $lm->id,
                'court_no'     => $lm->court_no,
                'match_type'   => ucfirst($lm->match_type),
                'division'     => $lm->division,
                'round_label'  => $getRoundLabel($lm->round),
                'player1_id'   => $lm->player1_id,
                'player2_id'   => $lm->player2_id,
                'p1_season_id' => $p1['season_id'],
                'p1_name'      => $p1['name'],
                'p1_city'      => $p1['city'],
                'p2_season_id' => $p2['season_id'],
                'p2_name'      => $p2['name'],
                'p2_city'      => $p2['city'],
                'score_p1'     => $lm->score_p1    ?? 0,
                'score_p2'     => $lm->score_p2    ?? 0,
                'sets_won_p1'  => $lm->sets_won_p1 ?? 0,
                'sets_won_p2'  => $lm->sets_won_p2 ?? 0,
                'sets_to_win'  => $lm->sets_to_win,
                'umpire_name'  => $lm->umpire_name,
            ];
        });

        return response()->json([
            'count'   => $liveMatches->count(),
            'matches' => $matches,
        ]);
    }
}