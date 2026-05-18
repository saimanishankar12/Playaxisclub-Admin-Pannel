<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\TournamentSeason;
use Illuminate\Http\Request;

/**
 * Shows total amount received in Ekalavya Badminton Tournament (Singles + Doubles).
 * Filterable by age category.
 */
class EkalavyaBadmintonUserController extends Controller
{
    public function index(Request $request)
    {
        $ageCategory = $request->get('age_category'); // U11|U13|U15|U19|null

        // ── Singles revenue ──────────────────────────────────────────────────
        $singlesQuery = Payment::where('status', 'paid')
            ->where('registration_type', 'single');

        if ($ageCategory) {
            // Join players table to filter by age_category
            $singlesQuery->whereHas('player', fn($q) => $q->where('age_category', $ageCategory));
        }

        $singlesRevenue = $singlesQuery->sum('amount');
        $singlesCount   = $singlesQuery->count();

        // ── Doubles revenue ──────────────────────────────────────────────────
        $doublesQuery = Payment::where('status', 'paid')
            ->where('registration_type', 'double');

        if ($ageCategory) {
            $doublesQuery->whereHas('player', fn($q) => $q->where('age_category', $ageCategory));
        }

        $doublesRevenue = $doublesQuery->sum('amount');
        $doublesCount   = $doublesQuery->count();

        // ── Totals ───────────────────────────────────────────────────────────
        $totalRevenue = $singlesRevenue + $doublesRevenue;
        $totalCount   = $singlesCount   + $doublesCount;

        // ── Breakdown per age category ────────────────────────────────────────
        $ageBreakdown = collect(['U11', 'U13', 'U15', 'U19'])->map(function ($cat) {
            $amount = Payment::where('status', 'paid')
                ->whereHas('player', fn($q) => $q->where('age_category', $cat))
                ->sum('amount');
            $count = Payment::where('status', 'paid')
                ->whereHas('player', fn($q) => $q->where('age_category', $cat))
                ->count();
            return ['category' => $cat, 'amount' => $amount, 'count' => $count];
        });

        return view('Admin.ekalavya.revenue', compact(
            'singlesRevenue', 'singlesCount',
            'doublesRevenue', 'doublesCount',
            'totalRevenue',   'totalCount',
            'ageBreakdown',   'ageCategory'
        ));
    }
}
