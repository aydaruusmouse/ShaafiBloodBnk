<?php

namespace App\Http\Controllers;

use App\Models\BloodBag;
use App\Models\Donor;
use App\Models\Patient;
use App\Models\Transfusion;
use App\Models\TransfusionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BloodInventory;

class BloodBagController extends Controller
{
    public function index(Donor $donor = null)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $query = BloodBag::with('donor', 'patient');
        
        if ($donor) {
            $query->where('donor_id', $donor->id);
        }
        
        $bloodBags = $query->latest()->paginate(10);
        
        if ($donor) {
            return view('blood-bags.index', compact('bloodBags', 'donor'));
        }
        
        return view('blood-bags.index', compact('bloodBags'));
    }
    

    public function create(Request $request, Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        if (!$donor) {
            return redirect()->route('donors.index')
                ->with('error', 'Please select a donor first to add a blood bag.');
        }

        // 1) List of donors (either just the one, or all legible)
        $donors = collect([$donor]);

        // 2) All existing patients (for optional family replacement lookup)
        $patients = Patient::all();

        // 3) Default values:
        $defaults = [
            'collection_date' => now()->format('Y-m-d'),
            'expiry_date'     => now()->addDays(35)->format('Y-m-d'),
            'donor_type'      => 'volunteer',
            'component_type'  => 'whole_blood',
            'status'          => 'available',
            'collected_by'    => auth()->user()->name,
        ];

        return view('blood-bags.create', compact(
            'donors','patients','donor','defaults'
        ));
    }

    public function store(Request $request, Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        try {
            // Log the incoming request data for debugging
            \Log::info('Blood bag creation request:', [
                'request_data' => $request->all(),
                'donor' => $donor->toArray()
            ]);

            // Map component type to database value
            $componentTypeMap = [
                'whole' => 'whole_blood',
                'plasma' => 'plasma',
                'rbc' => 'rbc',
                'platelets' => 'platelets'
            ];

            $componentType = $componentTypeMap[$request->input('components')[0]] ?? 'whole_blood';

            // Check donation frequency
            $lastDonation = BloodBag::where('donor_id', $donor->id)
                ->where('status', '!=', 'discarded')
                ->latest('collection_date')
                ->first();

            if ($lastDonation) {
                $collectionDate = $lastDonation->collection_date;
                $nextEligibleDate = $collectionDate->addDays(match($componentType) {
                    'whole_blood' => 56,
                    'plasma' => 28,
                    'platelets' => 7,
                    'rbc' => 56,
                    default => 56
                });

                if (now()->lt($nextEligibleDate)) {
                    $daysRemaining = floor(now()->floatDiffInDays($nextEligibleDate));
                    $hoursRemaining = floor(now()->floatDiffInHours($nextEligibleDate) % 24);
                    
                    $timeMessage = $daysRemaining > 0 
                        ? "{$daysRemaining} days and {$hoursRemaining} hours"
                        : "{$hoursRemaining} hours";

                    return back()
                        ->withInput()
                        ->with('error', "This donor cannot donate {$componentType} yet. Next eligible donation date is {$nextEligibleDate->format('M d, Y')} ({$timeMessage} from now).");
                }
            }

            // Validate the request
            $validated = $request->validate([
                'donor_id' => 'required|exists:donors,id',
                'serial_number' => 'required|string|unique:blood_bags,serial_number',
                'volume' => 'required|numeric|min:0|max:1000',
                'collection_date' => 'required|date',
                'donor_type' => 'required|in:volunteer,family_replacement',
                'components' => 'required|array',
                'components.*' => 'in:whole,plasma,rbc,platelets',
                'patient_name' => 'required_if:donor_type,family_replacement',
                'patient_phone' => 'nullable|string|max:20',
                'patient_mrn' => 'nullable|string|max:50',
                'patient_address' => 'nullable|string|max:255',
                'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'status' => 'required|in:available,reserved,transfused,expired,discarded',
                'collected_by' => 'required|string',
                'expiry_date' => 'required|date|after:collection_date',
            ]);

            // Start a database transaction
            DB::beginTransaction();

            $patientId = null;

            // If it's a family replacement, create or update patient
            if ($validated['donor_type'] === 'family_replacement' && !empty($validated['patient_name'])) {
                $patientData = [
                    'name' => $validated['patient_name'],
                    'medical_record_number' => $validated['patient_mrn'] ?? null,
                    'blood_group' => $validated['blood_group'],
                    'phone' => $validated['patient_phone'] ?? null,
                    'address' => $validated['patient_address'] ?? null,
                    'medical_history' => $validated['patient_medical_history'] ?? null,
                ];

                $patient = Patient::where('medical_record_number', $validated['patient_mrn'])->first();
                if (!$patient) {
                    $patient = Patient::create($patientData);
                } else {
                    $patient->update($patientData);
                }

                $patientId = $patient->id;
            }

            // Create the blood bag
            $bloodBag = new BloodBag([
                'donor_id' => $validated['donor_id'],
                'patient_id' => $patientId,
                'serial_number' => $validated['serial_number'],
                'volume' => $validated['volume'],
                'collection_date' => $validated['collection_date'],
                'donor_type' => $validated['donor_type'],
                'component_type' => $componentType,
                'blood_group' => $validated['blood_group'],
                'status' => $validated['status'],
                'collected_by' => $validated['collected_by'],
                'expiry_date' => $validated['expiry_date'],
            ]);

            $bloodBag->save();

            // Ensure donor status remains as Legible
            if ($donor->status !== 'Legible') {
                $donor->update(['status' => 'Legible']);
            }

            // Commit the transaction
            DB::commit();

            return redirect()
                ->route('blood-bags.index')
                ->with('success', 'Blood bag added successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            
            \Log::error('Blood bag creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'donor' => $donor->toArray()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create blood bag: ' . $e->getMessage());
        }
    }

    public function show(Donor $donor, BloodBag $bloodBag)
    {
        // Ensure the blood bag belongs to the donor
        if ($bloodBag->donor_id !== $donor->id) {
            abort(404);
        }

        $bloodBag->load('donor', 'patient', 'crossMatches', 'transfusions');
        return view('blood-bags.show', compact('bloodBag', 'donor'));
    }

    public function edit(BloodBag $bloodBag)
    {
        $donors = Donor::where('is_eligible', true)->get();
        return view('blood-bags.edit', compact('bloodBag', 'donors'));
    }

    public function update(Request $request, BloodBag $bloodBag)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'blood_group' => 'required|string|max:5',
            'component_type' => 'required|in:whole_blood,rbc,plasma,platelets',
            'volume' => 'required|numeric|min:0',
            'collection_date' => 'required|date',
            'expiry_date' => 'required|date|after:collection_date',
            'status' => 'required|in:available,reserved,transfused,expired,discarded',
            'collected_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $bloodBag->update($validated);

        return redirect()->route('blood-bags.show', $bloodBag)
            ->with('success', 'Blood bag information updated successfully.');
    }

    public function destroy(BloodBag $bloodBag)
    {
        $bloodBag->delete();
        return redirect()->route('blood-bags.index')
            ->with('success', 'Blood bag deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $bloodBags = BloodBag::where(function($q) use ($query) {
            $q->where('serial_number', 'like', "%{$query}%")
              ->orWhere('blood_group', 'like', "%{$query}%")
              ->orWhere('component_type', 'like', "%{$query}%")
              ->orWhereHas('donor', function($q) use ($query) {
                  $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('tell', 'like', "%{$query}%");
              });
        })
        ->with('donor')
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('blood-bags.index', compact('bloodBags'));
    }

    public function inventory()
    {
        $inventory = BloodBag::select('blood_group', 'component_type', 'status', DB::raw('count(*) as count'))
            ->groupBy('blood_group', 'component_type', 'status')
            ->get();

        return view('blood-bags.inventory', compact('inventory'));
    }

    public function process(Request $request)
    {
        // Debug: Log the entire request
        \Log::info('Process method called with request data:', $request->all());
        
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        \Log::info('Tenant context set:', ['tenantId' => app()->has('tenantId') ? app('tenantId') : 'not set']);
        
        $validated = $request->validate([
            'selected_bags' => 'required|array',
            'selected_bags.*' => 'required|exists:blood_bags,id'
        ]);

        // Debug: Log the selected bag IDs and their statuses
        \Log::info('Processing blood bags:', [
            'selected_bags' => $validated['selected_bags'],
            'user_hospital_id' => $user->hospital_id ?? 'null'
        ]);

        $allSelectedBags = BloodBag::whereIn('id', $validated['selected_bags'])->get();
        \Log::info('All selected bags:', $allSelectedBags->toArray());

        $selectedBags = BloodBag::whereIn('id', $validated['selected_bags'])
            ->where('status', 'available')
            ->get();

        \Log::info('Available selected bags:', $selectedBags->toArray());

        if ($selectedBags->isEmpty()) {
            return redirect()->route('blood-bags.index')
                ->with('error', 'No valid blood bags selected. Only bags with "available" status can be processed.');
        }

        return view('blood-bags.process', compact('selectedBags'));
    }

    public function transfuse(Request $request)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $bloodBagIds = explode(',', $request->bags);
        $bloodBags = BloodBag::whereIn('id', $bloodBagIds)
            ->where('status', 'available')
            ->get();

        if ($bloodBags->isEmpty()) {
            return redirect()->route('blood-bags.index')
                ->with('error', 'No valid blood bags selected.');
        }

        return view('blood-bags.transfuse', [
            'bloodBagIds' => $request->bags,
            'bloodBags' => $bloodBags
        ]);
    }

    public function completeTransfusion(Request $request)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        // Debug: Log the request data
        \Log::info('Complete transfusion request:', $request->all());
        
        $request->validate([
            'blood_bag_ids' => 'required|string',
            'patient_name' => 'required|string|max:255',
            'patient_blood_group' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'patient_age' => 'required|integer|min:0|max:120',
            'patient_gender' => 'required|string|in:male,female,other',
            'patient_medical_history' => 'nullable|string',
            'transfusion_date' => 'required|date',
            'transfusion_reason' => 'required|string|max:255',
            'transfusion_notes' => 'nullable|string',
        ]);

        $bloodBagIds = explode(',', $request->blood_bag_ids);
        \Log::info('Blood bag IDs:', ['ids' => $bloodBagIds]);
        
        $bloodBags = BloodBag::whereIn('id', $bloodBagIds)
            ->where('status', 'available')
            ->get();

        \Log::info('Found blood bags:', ['count' => $bloodBags->count(), 'bags' => $bloodBags->toArray()]);

        if ($bloodBags->isEmpty()) {
            return redirect()->route('blood-bags.index')
                ->with('error', 'No valid blood bags selected.');
        }

        // Create or find patient
        $patient = Patient::firstOrCreate(
            ['name' => $request->patient_name],
            [
                'blood_group' => $request->patient_blood_group,
                'age' => $request->patient_age,
                'gender' => $request->patient_gender,
                'medical_history' => $request->patient_medical_history,
                'hospital_id' => $user->hospital_id,
            ]
        );

        // Create transfusion record for each blood bag
        $transfusions = [];
        foreach ($bloodBags as $bag) {
            $transfusion = Transfusion::create([
                'patient_id' => $patient->id,
                'blood_bag_id' => $bag->id,
                'hospital_id' => $user->hospital_id,
                'transfusion_date' => $request->transfusion_date,
                'reason' => $request->transfusion_reason,
                'notes' => $request->transfusion_notes,
                'performed_by' => auth()->id(),
                // Optional fields - can be null for direct transfusions
                'blood_request_id' => null,
                'department_id' => null,
            ]);

            // Update blood bag status
            $bag->update(['status' => 'transfused']);
            
            $transfusions[] = $transfusion;
        }

        return redirect()->route('transfusions.index')
            ->with('success', 'Blood transfusion(s) completed successfully.');
    }

    public function processSelected(Request $request)
    {
        $request->validate([
            'selected_bags' => 'required|array',
            'selected_bags.*' => 'exists:blood_bags,id'
        ]);

        $selectedBags = BloodBag::whereIn('id', $request->selected_bags)
            ->where('status', 'pending')
            ->get();

        if ($selectedBags->isEmpty()) {
            return back()->with('error', 'No valid blood bags selected for processing.');
        }

        try {
            DB::beginTransaction();

            foreach ($selectedBags as $bag) {
                // Update blood bag status
                $bag->update([
                    'status' => 'processed',
                    'processed_at' => now()
                ]);

                // Create inventory record
                BloodInventory::create([
                    'blood_bag_id' => $bag->id,
                    'blood_group' => $bag->blood_group,
                    'component_type' => $bag->component_type,
                    'volume' => $bag->volume,
                    'status' => 'available',
                    'expiry_date' => now()->addDays(42), // 42 days shelf life
                    'storage_location' => 'Main Storage'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Selected blood bags have been processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process blood bags. Please try again.');
        }
    }
}
