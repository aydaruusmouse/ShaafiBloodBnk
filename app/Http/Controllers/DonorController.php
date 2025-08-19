<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Labtest;
use Illuminate\Support\Facades\DB; 
class DonorController extends Controller
{
    public function index()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        // Debug logging
        Log::info('DonorController@index', [
            'user_id' => auth()->id(),
            'user_hospital_id' => optional(auth()->user())->hospital_id,
            'tenantId' => app()->has('tenantId') ? app('tenantId') : 'not set',
            'donors_count' => Donor::count()
        ]);
        
        $donors = Donor::latest()->paginate(10);
        return view('donors.index', compact('donors'));
    }

    public function create()
    {
        return view('donors.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'age' => 'required|integer|min:18|max:65',
            'occupation' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'tell' => 'required|string|max:255',
            'weight' => 'nullable|numeric',
            'bp' => 'nullable|string|max:255',
            'hemoglobin' => 'nullable|string|max:255',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        // Set initial status as Pending
        $validated['status'] = 'Pending';

        // Ensure hospital_id is set for multi-tenancy
        if (auth()->check() && auth()->user()->hospital_id) {
            $validated['hospital_id'] = auth()->user()->hospital_id;
        }

        Donor::create($validated);

        return redirect()->route('donors.index')
            ->with('success', 'Donor registered successfully.');
    }

    

    public function labResultsIndex()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        // Get donors who have lab test records
        $donors = Donor::whereHas('labTests')
            ->whereIn('status', ['Legible', 'Illegible'])
            ->select('id', 'first_name', 'last_name', 'tell', 'blood_group', 'status')
            ->latest()
            ->paginate(10);



        return view('donors.lab-results.index', compact('donors'));
    }
   
      

//     public function labResultsIndex()
// {
//     // Get donors who have lab tests with their latest test
//     $donors = Donor::whereHas('labTests')
//         ->with(['labTests' => function($query) {
//             $query->latest()->first();
//         }])
//         ->latest()
//         ->paginate(10);
        
//     return view('donors.lab-results.index', compact('donors'));
// }

public function showLabResult(Donor $donor, $testId = null)
{ 
    $allTests = $donor->labTests()->orderBy('test_date','desc')->get();
    return view('donors.lab-results.show', compact('donor','allTests'));
    // If no test ID is provided, get the latest test
    if (!$testId) {
        $labTest = $donor->labTests()->latest()->firstOrFail();
    } else {
        $labTest = $donor->labTests()->findOrFail($testId);
    }
    
    // Get all tests for history
    $allTests = $donor->labTests()->orderBy('test_date', 'desc')->get();
    
    return view('donors.lab-results.show', compact('donor', 'labTest', 'allTests'));
}
    public function show(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        return view('donors.show', compact('donor'));
    }

    public function edit(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        return view('donors.edit', compact('donor'));
    }

    public function update(Request $request, Donor $donor)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'age' => 'required|integer|min:18|max:65',
            'occupation' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'tell' => 'required|string|max:255',
            'weight' => 'required|numeric',
            'bp' => 'required|string|max:255',
            'hemoglobin' => 'required|string|max:255',
            'blood_group' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        $donor->update($validated);

        return redirect()->route('donors.index')
            ->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $donor->delete();

        return redirect()->route('donors.index')
            ->with('success', 'Donor deleted successfully.');
    }

    public function assignLabTest(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        return view('donors.assign-lab-test', compact('donor'));
    }

    public function storeLabTest(Request $request, Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $validated = $request->validate([
            'test_type' => 'required|string|max:255',
            'test_date' => 'required|date',
            'result' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            // Map test_type to the correct column
            $testColumns = [
                'hiv' => 'hiv',
                'hepatitis_b' => 'hepatitis_b',
                'hepatitis_c' => 'hepatitis_c',
                'syphilis' => 'syphilis',
            ];

            $column = $testColumns[$validated['test_type']] ?? null;

            if ($column) {
                $labTest = \App\Models\LabTest::firstOrNew(['donor_id' => $donor->id]);
                $labTest->$column = $validated['result'] === 'negative'; // Save as boolean
                $labTest->tested_by = auth()->user()->id;
                $labTest->test_date = $validated['test_date'];
                $labTest->notes = $validated['notes'] ?? null;
                $labTest->save();
            } else {
                return back()->withErrors('Invalid test type.');
            }

            return redirect()->route('donors.index')
                ->with('success', 'Lab test results saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to save lab test results', [
                'error' => $e->getMessage(),
                'donor_id' => $donor->id,
                'data' => $request->all(),
            ]);
            return back()->withErrors('An error occurred while saving lab test results. Please try again.');
        }
    }

    public function showLabTestSelection(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $tests = [
            'hiv' => 'HIV',
            'hepatitis_b' => 'Hepatitis B',
            'hepatitis_c' => 'Hepatitis C',
            'syphilis' => 'Syphilis',
        ];
        return view('donors.lab-test-select', compact('donor', 'tests'));
    }

    public function postLabTestSelection(Request $request, Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $validated = $request->validate([
            'tests' => 'required|array',
            'tests.*' => 'in:hiv,hepatitis_b,hepatitis_c,syphilis',
        ]);
        session(['selected_tests_' . $donor->id => $validated['tests']]);
        return redirect()->route('donors.lab-test.results', $donor);
    }

    public function showLabTestResults(Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $tests = session('selected_tests_' . $donor->id, []);
        if (empty($tests)) {
            return redirect()->route('donors.lab-test.select', $donor)->withErrors('Please select at least one test.');
        }
        $testLabels = [
            'hiv' => 'HIV I/II (HIV-1/2), Antigen/Antibodies',
            'hepatitis_b' => 'Hepatitis B Surface Antigen (HBsAg)',
            'hepatitis_c' => 'Hepatitis C Virus (HCV) Antibody',
            'syphilis' => 'Syphilis',
        ];
        return view('donors.lab-test-results', compact('donor', 'tests', 'testLabels'));
    }

  

    public function postLabTestResults(Request $request, Donor $donor)
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        $tests = session('selected_tests_' . $donor->id, []);
        $rules = [];
        foreach ($tests as $test) {
            $rules["results.$test"] = 'required|in:positive,negative,inconclusive';
        }
        $validated = $request->validate($rules);

        try {
            $labTestData = [
                'donor_id' => $donor->id,
                'hiv' => $validated['results']['hiv'] ?? null,
                'hepatitis_b' => $validated['results']['hepatitis_b'] ?? null,
                'hepatitis_c' => $validated['results']['hepatitis_c'] ?? null,
                'syphilis' => $validated['results']['syphilis'] ?? null,
                'tested_by' => auth()->user()->id,
                'test_date' => now(),
                'notes' => null,
            ];

            \App\Models\LabTest::updateOrCreate(['donor_id' => $donor->id], $labTestData);

            // Determine donor status
            $status = 'Legible';
            $positiveTests = [];
            foreach (['hiv', 'hepatitis_b', 'hepatitis_c', 'syphilis'] as $test) {
                if (($labTestData[$test] ?? null) !== 'negative') {
                    $status = 'Illegible';
                    if (($labTestData[$test] ?? null) === 'positive') {
                        $positiveTests[] = ucfirst(str_replace('_', ' ', $test));
                    }
                }
            }

            // Update donor status
            $donor->update(['status' => $status]);

            session()->forget('selected_tests_' . $donor->id);
            $successMsg = 'Lab test results saved successfully.';
            if ($status === 'Illegible' && count($positiveTests)) {
                $successMsg .= ' Please consult for: ' . implode(', ', $positiveTests) . '.';
            }
            return redirect()->route('donors.index')->with('success', $successMsg);
        } catch (\Exception $e) {
            Log::error('Failed to save lab test results', [
                'error' => $e->getMessage(),
                'donor_id' => $donor->id,
                'data' => $request->all(),
            ]);
            return back()->withErrors('An error occurred while saving lab test results. Please try again.');
        }
    }

    public function withLabResults()
    {
        // Ensure tenant context is set for multi-tenancy
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        // Debug logging
        Log::info('withLabResults', [
            'user_id' => auth()->id(),
            'user_hospital_id' => optional(auth()->user())->hospital_id,
            'tenantId' => app()->has('tenantId') ? app('tenantId') : 'not set',
            'donors_with_lab_tests' => Donor::whereHas('labTests')->whereIn('status', ['Legible', 'Illegible'])->count()
        ]);
        
        // Get donors who have lab test records
        $donors = Donor::whereHas('labTests')
            ->whereIn('status', ['Legible', 'Illegible'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('donors.with-lab-results', compact('donors'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $donors = Donor::where(function($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('tell', 'like', "%{$query}%")
              ->orWhere('blood_group', 'like', "%{$query}%")
              ->orWhere('id', 'like', "%{$query}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('donors.index', compact('donors', 'query'));
    }
}
