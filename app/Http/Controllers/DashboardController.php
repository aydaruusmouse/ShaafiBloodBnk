<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\BloodRequest;
use App\Models\BloodBag;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
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
            'recentDonors',
            'recentRequests',
            'recentTransfusions'
        ));
    }
} 