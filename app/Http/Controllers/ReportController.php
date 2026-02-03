<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Hospital;
use App\Models\Department;
use App\Models\Donor;
use App\Models\BloodBag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /** Unique two-character codes per month (Jan–Dec), same as dashboard */
    protected const MONTH_CODES = [
        1 => 'JA', 2 => 'FE', 3 => 'MR', 4 => 'AP', 5 => 'MA', 6 => 'JN',
        7 => 'JL', 8 => 'AU', 9 => 'SE', 10 => 'OC', 11 => 'NO', 12 => 'DE',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function bloodRequests(Request $request)
    {
        $query = BloodRequest::with(['hospital', 'department', 'patient']);

        // Handle date range
        if ($request->filled('start_date')) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Handle other filters
        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', $request->hospital_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        // Get the results with pagination
        $bloodRequests = $query->latest()->paginate(15);

        // Get filter options
        $hospitals = Hospital::pluck('name', 'id');
        $departments = Department::pluck('name', 'id');
        $bloodGroups = BloodRequest::distinct()->pluck('blood_group');
        $urgencyLevels = ['low', 'medium', 'high'];

        return view('reports.blood-requests', compact(
            'bloodRequests',
            'hospitals',
            'departments',
            'bloodGroups',
            'urgencyLevels'
        ));
    }

    public function bloodTypeDistribution()
    {
        $distribution = BloodRequest::select('blood_group', DB::raw('count(*) as total'))
            ->groupBy('blood_group')
            ->get();

        return view('reports.blood-type-distribution', compact('distribution'));
    }

    public function hospitalStatistics()
    {
        $statistics = Hospital::withCount('bloodRequests')
            ->withCount(['bloodRequests as urgent_requests' => function($query) {
                $query->where('urgency', 'high');
            }])
            ->get();

        return view('reports.hospital-statistics', compact('statistics'));
    }

    public function departmentStatistics()
    {
        $statistics = Department::withCount('bloodRequests')
            ->withCount(['bloodRequests as urgent_requests' => function($query) {
                $query->where('urgency', 'high');
            }])
            ->with('hospital')
            ->get();

        return view('reports.department-statistics', compact('statistics'));
    }

    public function exportBloodRequests(Request $request)
    {
        $query = BloodRequest::with(['hospital', 'department']);

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->end_date)->endOfDay());
        }
        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', $request->hospital_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $bloodRequests = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="blood-requests.csv"',
        ];

        $callback = function () use ($bloodRequests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Hospital', 'Department', 'Patient Name', 'Blood Type', 'Units Required',
                'Urgency', 'Required Date', 'Status', 'Created At'
            ]);
            foreach ($bloodRequests as $req) {
                fputcsv($file, [
                    $req->id,
                    $req->hospital?->name ?? '',
                    $req->department?->name ?? '',
                    $req->patient_name ?? $req->patient?->name ?? '',
                    $req->blood_group,
                    $req->units_required,
                    $req->urgency,
                    $req->required_date?->format('Y-m-d') ?? '',
                    $req->status,
                    $req->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Inventory report: blood inventory by status, blood group, expiry.
     */
    public function inventoryReport(Request $request)
    {
        $query = \App\Models\BloodInventory::with(['donor', 'storageLocation', 'component']);

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('expiring_within_days')) {
            $days = (int) $request->expiring_within_days;
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
        }

        $items = $query->latest('collection_date')->paginate(20);

        $bloodGroups = \App\Models\BloodInventory::distinct()->whereNotNull('blood_group')->pluck('blood_group')->sort()->values();
        $statuses = \App\Models\BloodInventory::distinct()->pluck('status')->filter()->sort()->values();

        $summary = [
            'total_units' => \App\Models\BloodInventory::count(),
            'available'   => \App\Models\BloodInventory::where('status', 'available')->count(),
            'expired'     => \App\Models\BloodInventory::whereDate('expiry_date', '<', now()->toDateString())->count(),
        ];

        return view('reports.inventory', compact('items', 'bloodGroups', 'statuses', 'summary'));
    }

    /**
     * Report filtered by month and optional blood group — same metrics as dashboard.
     */
    public function monthlySummary(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        $year = (int) ($request->filled('year') ? $request->year : now()->year);
        $monthNum = (int) ($request->filled('month') ? $request->month : now()->month);
        $bloodGroup = $request->filled('blood_group') ? $request->blood_group : null;

        $date = Carbon::createFromDate($year, $monthNum, 1);
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $prevStart = $date->copy()->subMonth()->startOfMonth();
        $prevEnd = $date->copy()->subMonth()->endOfMonth();

        $baseDonor = function ($q) use ($start, $end, $bloodGroup) {
            $q->whereBetween('created_at', [$start, $end]);
            if ($bloodGroup) {
                $q->where('blood_group', $bloodGroup);
            }
        };
        $baseRequest = function ($q) use ($start, $end, $bloodGroup) {
            $q->whereBetween('created_at', [$start, $end]);
            if ($bloodGroup) {
                $q->where('blood_group', $bloodGroup);
            }
        };
        $baseBag = function ($q) use ($start, $end, $bloodGroup) {
            $q->whereBetween('created_at', [$start, $end]);
            if ($bloodGroup) {
                $q->where('blood_group', $bloodGroup);
            }
        };

        $totalDonors = Donor::whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();
        $eligibleDonors = Donor::where('status', 'Legible')
            ->whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();
        $pendingRequests = BloodRequest::where('status', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();
        $availableUnits = BloodBag::where('status', 'available')
            ->whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();

        $donorsPrevMonth = Donor::whereBetween('created_at', [$prevStart, $prevEnd])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();
        $eligiblePrevMonth = Donor::where('status', 'Legible')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->count();

        $donorsIncreasePct = $donorsPrevMonth > 0
            ? (($totalDonors - $donorsPrevMonth) / $donorsPrevMonth) * 100
            : ($totalDonors > 0 ? 100.0 : 0.0);
        $eligibleIncreasePct = $eligiblePrevMonth > 0
            ? (($eligibleDonors - $eligiblePrevMonth) / $eligiblePrevMonth) * 100
            : ($eligibleDonors > 0 ? 100.0 : 0.0);

        $bloodGroupStats = Donor::whereNotNull('blood_group')
            ->whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->selectRaw('blood_group, count(*) as count')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();

        $monthBloodGroups = Donor::whereNotNull('blood_group')
            ->whereBetween('created_at', [$start, $end])
            ->when($bloodGroup, fn ($q) => $q->where('blood_group', $bloodGroup))
            ->selectRaw('blood_group, count(*) as count')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();
        $monthTotal = array_sum($monthBloodGroups) ?: 0;

        $monthlyOverview = [[
            'code' => self::MONTH_CODES[$monthNum],
            'label' => $date->format('F Y'),
            'short_label' => $date->format('M Y'),
            'total_donors' => $totalDonors,
            'eligible_donors' => $eligibleDonors,
            'pending_requests' => $pendingRequests,
            'available_units' => $availableUnits,
            'blood_group_stats' => $monthBloodGroups,
            'month_total' => $monthTotal,
        ]];

        $totalForPct = $totalDonors > 0 ? $totalDonors : 1;

        $monthsForSelect = [];
        for ($i = 0; $i < 24; $i++) {
            $d = now()->subMonths($i);
            $monthsForSelect[$d->format('Y-m')] = $d->format('F Y');
        }
        $bloodGroupsForSelect = Donor::distinct()->whereNotNull('blood_group')->orderBy('blood_group')->pluck('blood_group')->toArray();

        return view('reports.monthly-summary', compact(
            'totalDonors',
            'eligibleDonors',
            'pendingRequests',
            'availableUnits',
            'bloodGroupStats',
            'donorsIncreasePct',
            'eligibleIncreasePct',
            'monthlyOverview',
            'totalForPct',
            'monthsForSelect',
            'bloodGroupsForSelect',
            'year',
            'monthNum',
            'bloodGroup'
        ));
    }
}
