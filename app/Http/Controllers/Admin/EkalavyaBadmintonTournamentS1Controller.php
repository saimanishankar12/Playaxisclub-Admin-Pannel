<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Player;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;

/**
 * Handles Singles & Doubles player lists for Ekalavya Badminton Tournament S1.
 * Shows who is PAID and who is NOT PAID, filterable by age category.
 *
 * Season label for S1 = "Season 1"  (id=1 in tournament_seasons)
 * season_id in payments_data uses prefix:
 *   Singles  →  ALKxxxxS
 *   Doubles  →  ALKxxxxD
 */
class EkalavyaBadmintonTournamentS1Controller extends Controller
{
    // The tournament_seasons.id for Ekalavya S1
    private const SEASON_DB_ID = 1;

    // ── Singles list ─────────────────────────────────────────────────────────
    public function singles(Request $request)
    {
        $ageCategory = $request->get('age_category'); // U11|U13|U15|U19
        $payStatus   = $request->get('pay_status');   // paid|unpaid|all (default all)

        [$paid, $unpaid] = $this->getPlayers('single', $ageCategory);

        return view('Admin.ekalavya.singles', compact('paid', 'unpaid', 'ageCategory', 'payStatus'));
    }

    // ── Doubles list ─────────────────────────────────────────────────────────
    public function doubles(Request $request)
    {
        $ageCategory = $request->get('age_category');
        $payStatus   = $request->get('pay_status');

        [$paid, $unpaid] = $this->getPlayers('double', $ageCategory);

        return view('Admin.ekalavya.doubles', compact('paid', 'unpaid', 'ageCategory', 'payStatus'));
    }

    // ── Shared helper ─────────────────────────────────────────────────────────
    /**
     * Returns [paid_players, unpaid_players] for given match type.
     * "Paid"   = player has a row in payments_data with status='paid'
     *            and registration_type matching the mode.
     * "Unpaid" = registered player with no matching paid payment.
     */
    private function getPlayers(string $type, ?string $ageCategory): array
    {
        // Player IDs who PAID for this type
        $paidIds = Payment::where('status', 'paid')
            ->where('registration_type', $type)
            ->pluck('player1_id')
            ->unique();

        // All players of this type
        $query = Player::where('mode', $type);
        if ($ageCategory) {
            $query->where('age_category', $ageCategory);
        }

        $allPlayers = $query->orderBy('name')->get();

        $paid   = $allPlayers->whereIn('player_id', $paidIds);
        $unpaid = $allPlayers->whereNotIn('player_id', $paidIds);

        return [$paid, $unpaid];
    }
}
