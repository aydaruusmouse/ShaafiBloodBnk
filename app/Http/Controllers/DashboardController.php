<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\BloodRequest;
use App\Models\BloodBag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Unique two-character codes per month (Jan–Dec) */
    protected const MONTH_CODES = [
        1 => 'JA', 2 => 'FE', 3 => 'MR', 4 => 'AP', 5 => 'MA', 6 => 'JN',
        7 => 'JL', 8 => 'AU', 9 => 'SE', 10 => 'OC', 11 => 'NO', 12 => 'DE',
    ];

    public function index()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        // Get total donors count
        $totalDonors = Donor::count();

        // Get eligible donors count
        $eligibleDonors = Donor::where('status', 'Legible')->count();

        // Get pending blood requests count
        $pendingRequests = BloodRequest::where('status', 'pending')->count();

        // Get available blood units count
        $availableUnits = BloodBag::where('status', 'available')->count();

        // Get blood group distribution
        $bloodGroupStats = Donor::whereNotNull('blood_group')
            ->selectRaw('blood_group, count(*) as count')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();

        // Month-over-month change for summary cards (current month vs previous month)
        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $donorsThisMonth = Donor::whereBetween('created_at', [$currentMonthStart, $now])->count();
        $donorsPrevMonth = Donor::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count();
        $donorsIncreasePct = $this->percentChange($donorsThisMonth, $donorsPrevMonth);

        $eligibleThisMonth = Donor::where('status', 'Legible')->whereBetween('created_at', [$currentMonthStart, $now])->count();
        $eligiblePrevMonth = Donor::where('status', 'Legible')->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count();
        $eligibleIncreasePct = $this->percentChange($eligibleThisMonth, $eligiblePrevMonth);

        // Monthly overview: current month only with unique two-character code
        $month = (int) $now->format('n');
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $monthTotalDonors = Donor::whereBetween('created_at', [$start, $end])->count();
        $monthEligibleDonors = Donor::where('status', 'Legible')->whereBetween('created_at', [$start, $end])->count();
        $monthPendingRequests = BloodRequest::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count();
        $monthAvailableUnits = BloodBag::where('status', 'available')->whereBetween('created_at', [$start, $end])->count();

        $monthBloodGroups = Donor::whereNotNull('blood_group')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('blood_group, count(*) as count')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();

        $monthlyOverview = [[
            'code'       => self::MONTH_CODES[$month],
            'label'      => $now->format('F Y'),
            'short_label'=> $now->format('M Y'),
            'total_donors'      => $monthTotalDonors,
            'eligible_donors'   => $monthEligibleDonors,
            'pending_requests'  => $monthPendingRequests,
            'available_units'   => $monthAvailableUnits,
            'blood_group_stats' => $monthBloodGroups,
            'month_total'       => array_sum($monthBloodGroups) ?: 0,
        ]];

        // Get recent donors
        $recentDonors = Donor::latest()
            ->take(5)
            ->get();

        // Get recent blood requests
        $recentRequests = BloodRequest::with('hospital')
            ->latest()
            ->take(5)
            ->get();

        // Get recent transfusions
        $recentTransfusions = \App\Models\Transfusion::with(['patient', 'bloodBag'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalDonors',
            'eligibleDonors',
            'pendingRequests',
            'availableUnits',
            'bloodGroupStats',
            'donorsIncreasePct',
            'eligibleIncreasePct',
            'monthlyOverview',
            'recentDonors',
            'recentRequests',
            'recentTransfusions'
        ));
    }

    private function percentChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
        return (($current - $previous) / $previous) * 100;
    }
} 