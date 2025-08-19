<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\Department;
use App\Models\BloodBag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodRequestController extends Controller
{
    public function index()
    {
        $requests = BloodRequest::with(['hospital', 'department', 'requester'])
            ->latest()
            ->paginate(10);
            
        return view('blood-requests.index', compact('requests'));
    }

    public function create()
    {
        $hospitals = Hospital::all();
        $departments = Department::all();
        return view('blood-requests.create', compact('hospitals', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'units_needed' => 'required|integer|min:1',
            'patient_name' => 'required|string|max:255',
            'required_date' => 'required|date|after:today',
            'urgency' => 'required|in:low,medium,high',
            'notes' => 'nullable|string'
        ]);

        // Create or find patient
        $patient = Patient::firstOrCreate(
            ['name' => $validated['patient_name']],
            ['blood_group' => $validated['blood_group']]
        );

        // Create blood request
        $bloodRequest = BloodRequest::create([
            'hospital_id' => $validated['hospital_id'],
            'department_id' => $validated['department_id'],
            'patient_id' => $patient->id,
            'patient_name' => $validated['patient_name'],
            'blood_group' => $validated['blood_group'],
            'units_required' => $validated['units_needed'],
            'required_date' => $validated['required_date'],
            'urgency' => $validated['urgency'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'requested_by' => auth()->id()
        ]);

        return redirect()->route('blood-requests.show', $bloodRequest)
            ->with('success', 'Blood request created successfully.');
    }

    public function show(BloodRequest $bloodRequest)
    {
        $bloodRequest->load(['patient', 'hospital', 'department', 'crossMatches']);
        $availableBags = BloodBag::where('blood_group', $bloodRequest->blood_group)
            ->where('component_type', $bloodRequest->component_type)
            ->where('status', 'available')
            ->get();
        return view('blood-requests.show', compact('bloodRequest', 'availableBags'));
    }

    public function edit(BloodRequest $bloodRequest)
    {
        $hospitals = Hospital::where('status', 'active')->get();
        $departments = Department::all();
        
        return view('blood-requests.edit', compact('bloodRequest', 'hospitals', 'departments'));
    }

    public function update(Request $request, BloodRequest $bloodRequest)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'blood_type' => 'required|string',
            'units_needed' => 'required|integer|min:1',
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',
            'urgency_level' => 'required|in:normal,urgent,emergency',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
            'required_date' => 'required|date'
        ]);

        $bloodRequest->update($validated);

        return redirect()->route('blood-requests.index')
            ->with('success', 'Blood request updated successfully.');
    }

    public function destroy(BloodRequest $bloodRequest)
    {
        $bloodRequest->delete();

        return redirect()->route('blood-requests.index')
            ->with('success', 'Blood request deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $bloodRequests = BloodRequest::whereHas('patient', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('patient_id', 'like', "%{$query}%");
            })
            ->orWhereHas('hospital', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->paginate(10);

        return view('blood-requests.index', compact('bloodRequests'));
    }

    public function approve(BloodRequest $bloodRequest)
    {
        $bloodRequest->update(['status' => 'approved']);
        return redirect()->route('blood-requests.show', $bloodRequest)
            ->with('success', 'Blood request approved successfully.');
    }

    public function reject(BloodRequest $bloodRequest)
    {
        $bloodRequest->update(['status' => 'rejected']);
        return redirect()->route('blood-requests.show', $bloodRequest)
            ->with('success', 'Blood request rejected successfully.');
    }
}
