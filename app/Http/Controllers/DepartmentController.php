<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        $query = Department::with(['hospital'])->withCount('bloodRequests')->latest();

        // Super admin sees all; hospital users see their own
        if (!($user->role && $user->role->name === 'super_admin')) {
            $query->where('hospital_id', $user->hospital_id);
        }

        $departments = $query->paginate(10);
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        // Super admin can choose hospital; hospital users are locked to their hospital
        $hospitals = ($user->role && $user->role->name === 'super_admin')
            ? Hospital::withoutGlobalScopes()->where('status', 'active')->get()
            : Hospital::where('id', $user->hospital_id)->get();

        return view('departments.create', compact('hospitals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            if ($user && $user->hospital_id) {
                app()->instance('tenantId', $user->hospital_id);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'hospital_id' => 'nullable|exists:hospitals,id',
                'description' => 'nullable|string',
                'head_of_department' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255'
            ]);

            // Force hospital_id for non-super-admins
            if (!($user->role && $user->role->name === 'super_admin')) {
                $validated['hospital_id'] = $user->hospital_id;
            }

            Log::info('Validated data:', $validated);

            $department = Department::create($validated);

            Log::info('Department created:', ['id' => $department->id]);

            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating department: ' . $e->getMessage());
            Log::error('Request data:', $request->all());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create department. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        // Enforce access
        if (!($user->role && $user->role->name === 'super_admin') && $department->hospital_id !== $user->hospital_id) {
            abort(403, 'You can only view your hospital departments.');
        }

        $department->load(['hospital', 'bloodRequests']);
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $user = auth()->user();
        if ($user && $user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }

        if (!($user->role && $user->role->name === 'super_admin') && $department->hospital_id !== $user->hospital_id) {
            abort(403, 'You can only edit your hospital departments.');
        }

        $hospitals = ($user->role && $user->role->name === 'super_admin')
            ? Hospital::withoutGlobalScopes()->where('status', 'active')->get()
            : Hospital::where('id', $user->hospital_id)->get();

        return view('departments.edit', compact('department', 'hospitals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        try {
            $user = auth()->user();
            if ($user && $user->hospital_id) {
                app()->instance('tenantId', $user->hospital_id);
            }

            if (!($user->role && $user->role->name === 'super_admin') && $department->hospital_id !== $user->hospital_id) {
                abort(403, 'You can only update your hospital departments.');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'hospital_id' => 'nullable|exists:hospitals,id',
                'description' => 'nullable|string',
                'head_of_department' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255'
            ]);

            if (!($user->role && $user->role->name === 'super_admin')) {
                $validated['hospital_id'] = $user->hospital_id;
            }

            Log::info('Updating department:', ['id' => $department->id, 'data' => $validated]);

            $department->update($validated);

            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            Log::error('Request data:', $request->all());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update department. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        try {
            $user = auth()->user();
            if ($user && $user->hospital_id) {
                app()->instance('tenantId', $user->hospital_id);
            }

            if (!($user->role && $user->role->name === 'super_admin') && $department->hospital_id !== $user->hospital_id) {
                abort(403, 'You can only delete your hospital departments.');
            }

            $department->delete();
            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete department. Please try again.']);
        }
    }
}
