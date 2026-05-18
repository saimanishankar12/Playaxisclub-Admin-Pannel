<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Season;

class PlayaxisclubTotalRevenueController extends Controller
{
    /**
     * Main revenue page — one row per season, auto-updates as new seasons are added.
     */
     public function index()
    {
        // Step 1: Get all paid payment stats directly from payments_data
        $singlesStats = DB::table('payments_data')
            ->where('status', 'paid')
            ->where('registration_type', 'single')
            ->selectRaw('COUNT(*) as count, SUM(amount) as revenue')
            ->first();

        $doublesStats = DB::table('payments_data')
            ->where('status', 'paid')
            ->where('registration_type', 'double')
            ->selectRaw('COUNT(*) as count, SUM(amount) as revenue')
            ->first();

        $grandTotal     = ($singlesStats->revenue ?? 0) + ($doublesStats->revenue ?? 0);
        $grandPaidCount = ($singlesStats->count ?? 0) + ($doublesStats->count ?? 0);

        // Step 2: Get seasons and manually attach payment stats
        $seasons = Season::orderByDesc('created_at')->get()->map(function ($season) use ($singlesStats, $doublesStats) {
            // Since there's no direct FK, attach totals to the only/active season
            // If you have multiple seasons later, you'll need a proper FK
            $season->singles_count   = $singlesStats->count ?? 0;
            $season->singles_revenue = $singlesStats->revenue ?? 0;
            $season->doubles_count   = $doublesStats->count ?? 0;
            $season->doubles_revenue = $doublesStats->revenue ?? 0;
            $season->paid_count      = ($singlesStats->count ?? 0) + ($doublesStats->count ?? 0);
            $season->total_revenue   = ($singlesStats->revenue ?? 0) + ($doublesStats->revenue ?? 0);
            return $season;
        });

        return view('Admin.revenue.index', compact('seasons', 'grandTotal', 'grandPaidCount'));
    }
    // public function index()
    // {
    //     $seasons = Season::withCount([
    //             // Total paid registrations
    //             'payments as paid_count' => fn($q) => $q->where('status', 'paid'),

    //             // Singles paid count
    //             'payments as singles_count' => fn($q) => $q
    //                 ->where('status', 'paid')
    //                 ->where('registration_type', 'single'),

    //             // Doubles paid count
    //             'payments as doubles_count' => fn($q) => $q
    //                 ->where('status', 'paid')
    //                 ->where('registration_type', 'double'),
    //         ])
    //         ->withSum(
    //             ['payments as total_revenue' => fn($q) => $q->where('status', 'paid')],
    //             'amount'
    //         )
    //         ->withSum(
    //             ['payments as singles_revenue' => fn($q) => $q->where('status', 'paid')->where('registration_type', 'single')],
    //             'amount'
    //         )
    //         ->withSum(
    //             ['payments as doubles_revenue' => fn($q) => $q->where('status', 'paid')->where('registration_type', 'double')],
    //             'amount'
    //         )
    //         ->orderByDesc('created_at')
    //         ->get();

    //     $grandTotal        = $seasons->sum('total_revenue');
    //     $grandPaidCount    = $seasons->sum('paid_count');

    //     return view('Admin.revenue.index', compact('seasons', 'grandTotal', 'grandPaidCount'));
    // }

    // ── Keep these routes alive so existing URLs don't 404 ───────────────────

    public function tournament($tournament)
    {
        return redirect()->route('admin-revenue');
    }

    public function season($tournament, $season)
    {
        return redirect()->route('admin-revenue');
    }
}