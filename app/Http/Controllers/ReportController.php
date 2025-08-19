<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
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

        // Apply the same filters as in bloodRequests method
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', $request->hospital_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->blood_type);
        }

        if ($request->filled('urgency_level')) {
            $query->where('urgency_level', $request->urgency_level);
        }

        $bloodRequests = $query->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="blood-requests.csv"',
        ];

        $callback = function() use ($bloodRequests) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID',
                'Hospital',
                'Department',
                'Patient Name',
                'Blood Type',
                'Units Needed',
                'Urgency Level',
                'Required Date',
                'Status',
                'Created At'
            ]);

            // Add data
            foreach ($bloodRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->hospital->name,
                    $request->department->name,
                    $request->patient_name,
                    $request->blood_type,
                    $request->units_needed,
                    $request->urgency_level,
                    $request->required_date,
                    $request->status,
                    $request->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
