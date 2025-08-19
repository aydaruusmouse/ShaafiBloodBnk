<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin can see all hospitals
        if ($user->role && $user->role->name === 'super_admin') {
            $hospitals = Hospital::withoutGlobalScopes()
                ->withCount(['departments', 'bloodRequests'])
                ->latest()
                ->paginate(10);
        } else {
            // Hospital users can only see their own hospital
            $hospitals = Hospital::where('id', $user->hospital_id)
                ->withCount(['departments', 'bloodRequests'])
                ->latest()
                ->paginate(10);
        }
            
        return view('hospitals.index', compact('hospitals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hospitals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        Hospital::create($validated);

        return redirect()->route('hospitals.index')
            ->with('success', 'Hospital created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hospital $hospital)
    {
        $user = auth()->user();
        
        // Ensure tenant context is set for multi-tenancy
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
        
        // Check if user has access to this hospital
        if ($user->role && $user->role->name !== 'super_admin' && $user->hospital_id !== $hospital->id) {
            abort(403, 'You can only view your own hospital.');
        }
        
        // Load departments with proper tenant context
        if ($user->role && $user->role->name === 'super_admin') {
            // Super admin can see all departments for this hospital
            $departments = Department::withoutGlobalScopes()
                ->where('hospital_id', $hospital->id)
                ->withCount('bloodRequests')
                ->get();
        } else {
            // Hospital users see only their departments
            $departments = Department::where('hospital_id', $hospital->id)
                ->withCount('bloodRequests')
                ->get();
        }
        
        $hospital->load(['bloodRequests']);
        return view('hospitals.show', compact('hospital', 'departments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hospital $hospital)
    {
        $user = auth()->user();
        
        // Check if user has access to this hospital
        if ($user->role && $user->role->name !== 'super_admin' && $user->hospital_id !== $hospital->id) {
            abort(403, 'You can only edit your own hospital.');
        }
        
        return view('hospitals.edit', compact('hospital'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        $user = auth()->user();
        
        // Check if user has access to this hospital
        if ($user->role && $user->role->name !== 'super_admin' && $user->hospital_id !== $hospital->id) {
            abort(403, 'You can only update your own hospital.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $hospital->update($validated);

        return redirect()->route('hospitals.index')
            ->with('success', 'Hospital updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hospital $hospital)
    {
        $user = auth()->user();
        
        // Only Super Admin can delete hospitals
        if ($user->role && $user->role->name !== 'super_admin') {
            abort(403, 'Only Super Admin can delete hospitals.');
        }
        
        $hospital->delete();

        return redirect()->route('hospitals.index')
            ->with('success', 'Hospital deleted successfully.');
    }
}
