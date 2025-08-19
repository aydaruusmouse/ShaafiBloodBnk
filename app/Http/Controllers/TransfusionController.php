<?php

namespace App\Http\Controllers;

use App\Models\Transfusion;
use App\Models\BloodRequest;
use App\Models\BloodInventory;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransfusionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $transfusions = Transfusion::with(['patient', 'bloodBag'])
            ->latest()
            ->paginate(20);

        return view('transfusions.index', compact('transfusions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bloodRequest = null;
        
        if ($request->has('request_id')) {
            $bloodRequest = BloodRequest::with(['patient', 'hospital', 'department'])
                ->findOrFail($request->request_id);
                
            // Find compatible blood units
            $compatibleBlood = $this->findCompatibleBlood($bloodRequest->blood_group, $bloodRequest->component_type);
        } else {
            // Load all available blood bags if no request_id is provided
            $compatibleBlood = BloodInventory::where('status', 'available')
                ->where('expires_at', '>', now())
                ->with('donor')
                ->get();
        }

        $hospitals = Hospital::all();
        $departments = Department::all();
        
        return view('transfusions.create', compact('bloodRequest', 'compatibleBlood', 'hospitals', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blood_request_id' => 'nullable|exists:blood_requests,id',
            'blood_bag_id' => 'required|exists:blood_inventories,id',
            'patient_id' => 'required|exists:patients,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',
            'transfusion_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create the transfusion record
            $transfusion = new Transfusion();
            $transfusion->fill($validated);
            $transfusion->administered_by = Auth::id();
            $transfusion->save();

            // Update blood bag status to used
            $bloodBag = BloodInventory::findOrFail($validated['blood_bag_id']);
            $bloodBag->status = 'used';
            $bloodBag->save();

            // If this was from a blood request, update the request status
            if (!empty($validated['blood_request_id'])) {
                $bloodRequest = BloodRequest::findOrFail($validated['blood_request_id']);
                $bloodRequest->status = 'completed';
                $bloodRequest->save();
            }

            DB::commit();

            return redirect()
                ->route('transfusions.show', $transfusion->id)
                ->with('success', 'Blood transfusion recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record blood transfusion: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfusion $transfusion)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $transfusion->load(['patient', 'bloodBag']);
        return view('transfusions.show', compact('transfusion'));
    }

    /**
     * Find compatible blood units for a given blood group and component type
     */
    private function findCompatibleBlood($bloodGroup, $componentType = null)
    {
        $query = BloodInventory::where('status', 'available')
            ->where('expires_at', '>', now())
            ->where('blood_group', $bloodGroup);

        if ($componentType) {
            $query->where('component', $componentType);
        }

        return $query->with('donor')->get();
    }

    /**
     * Get compatible blood units via AJAX
     */
    public function getCompatibleBlood(Request $request)
    {
        $request->validate([
            'blood_group' => 'required|string',
            'component' => 'nullable|string',
        ]);

        $blood = $this->findCompatibleBlood(
            $request->blood_group,
            $request->component
        );

        return response()->json($blood);
    }
}
