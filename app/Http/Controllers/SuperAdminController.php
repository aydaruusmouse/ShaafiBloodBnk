<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (optional(auth()->user()->role)->name !== 'super_admin') {
                abort(403, 'Access denied. Super Admin only.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        // System-wide statistics (all hospitals combined)
        $stats = [
            'total_hospitals' => Hospital::withoutGlobalScopes()->count(),
            'active_hospitals' => Hospital::withoutGlobalScopes()->where('status', 'active')->count(),
            'inactive_hospitals' => Hospital::withoutGlobalScopes()->where('status', 'inactive')->count(),
            'total_donors' => \App\Models\Donor::withoutGlobalScopes()->count(),
            'total_patients' => \App\Models\Patient::withoutGlobalScopes()->count(),
            'total_blood_requests' => \App\Models\BloodRequest::withoutGlobalScopes()->count(),
            'total_transfusions' => \App\Models\Transfusion::withoutGlobalScopes()->count(),
            'hospitals_by_city' => Hospital::withoutGlobalScopes()->select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->get()
        ];

        return view('super-admin.dashboard', compact('stats'));
    }

    public function hospitals()
    {
        $hospitals = Hospital::withoutGlobalScopes()->withCount(['departments', 'users'])
            ->when(request('city'), function ($query, $city) {
                $query->where('city', 'like', "%{$city}%");
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);

        return view('super-admin.hospitals.index', compact('hospitals'));
    }

    public function createHospital()
    {
        return view('super-admin.hospitals.create');
    }

    public function storeHospital(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_phone' => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {
            // Create hospital
            $hospital = Hospital::withoutGlobalScopes()->create([
                'name' => $validated['name'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            // Create initial hospital admin user
            $hospitalAdminRole = Role::where('name', 'hospital_admin')->first();
            
            $adminUser = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'phone' => $validated['admin_phone'],
                'role_id' => $hospitalAdminRole->id,
                'hospital_id' => $hospital->id,
                'password' => Hash::make('ChangeMe123!'),
                'status' => 'active',
            ]);

            // Store credentials for display
            session()->flash('hospital_credentials', [
                'hospital' => $hospital->name,
                'admin_email' => $adminUser->email,
                'admin_password' => 'ChangeMe123!'
            ]);
        });

        return redirect()->route('super-admin.hospitals')
            ->with('success', 'Hospital created successfully with initial admin user.');
    }

    public function editHospital(Hospital $hospital)
    {
        // Remove global scopes for Super Admin editing
        $hospital = Hospital::withoutGlobalScopes()->findOrFail($hospital->id);
        return view('super-admin.hospitals.edit', compact('hospital'));
    }

    public function updateHospital(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Remove global scopes for Super Admin updating
        $hospital = Hospital::withoutGlobalScopes()->findOrFail($hospital->id);
        $hospital->update($validated);

        return redirect()->route('super-admin.hospitals')
            ->with('success', 'Hospital updated successfully.');
    }

    public function resetHospitalAdmin(Hospital $hospital)
    {
        $adminUser = User::withoutGlobalScopes()->where('hospital_id', $hospital->id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'hospital_admin');
            })
            ->first();

        if ($adminUser) {
            $newPassword = 'ChangeMe' . rand(100, 999) . '!';
            $adminUser->update(['password' => Hash::make($newPassword)]);
            
            session()->flash('reset_credentials', [
                'hospital' => $hospital->name,
                'admin_email' => $adminUser->email,
                'admin_password' => $newPassword
            ]);
        }

        return redirect()->route('super-admin.hospitals')
            ->with('success', 'Hospital admin password reset successfully.');
    }

    public function switchTenant(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,id'
        ]);

        $request->session()->put('active_hospital_id', (int) $request->hospital_id);
        
        return redirect()->back()
            ->with('success', 'Switched to hospital context successfully.');
    }

    public function clearTenantContext(Request $request)
    {
        $request->session()->forget('active_hospital_id');
        
        return redirect()->back()
            ->with('success', 'Cleared hospital context successfully.');
    }
} 