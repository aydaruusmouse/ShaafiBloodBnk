<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $patients = Patient::latest()
            ->paginate(10);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'blood_group' => 'required|string|max:5',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
        ]);

        $validated['patient_id'] = 'PAT-' . Str::random(8);

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $patient->load(['bloodRequests', 'crossMatches', 'transfusions']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'blood_group' => 'required|string|max:5',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient information updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $patients = Patient::where('patient_id', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->paginate(10);

        return view('patients.index', compact('patients'));
    }
}
