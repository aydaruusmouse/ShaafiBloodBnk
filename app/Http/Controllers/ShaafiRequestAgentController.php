<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\ShaafiRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShaafiRequestAgentController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->scopedQuery()->with(['hospital', 'reviewer']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('external_reference', 'like', "%{$search}%");

                if (! str_starts_with(strtoupper($search), 'SR-')) {
                    $q->orWhere('reference_number', 'like', '%SR-' . $search . '%');
                }
            });
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->whereRaw('LOWER(city) = ?', [strtolower($request->city)]);
        }

        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', $request->hospital_id);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $cities = Hospital::where('status', 'active')
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        $hospitals = Hospital::where('status', 'active')->orderBy('name')->get(['id', 'name', 'city']);

        $pendingCount = $this->scopedQuery()->where('status', 'pending')->count();
        $scopeMeta = $this->scopeMeta();
        $totalInDatabase = ShaafiRequest::count();

        return view('shaafi-requests.index', compact(
            'requests', 'cities', 'hospitals', 'pendingCount', 'scopeMeta', 'totalInDatabase'
        ));
    }

    public function show(ShaafiRequest $shaafiRequest)
    {
        $this->authorizeRequest($shaafiRequest);

        $shaafiRequest->load(['hospital', 'reviewer']);

        return view('shaafi-requests.show', compact('shaafiRequest'));
    }

    public function updateStatus(Request $request, ShaafiRequest $shaafiRequest)
    {
        $this->authorizeRequest($shaafiRequest);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'under_review', 'approved', 'rejected', 'scheduled', 'completed', 'cancelled',
            ])],
            'agent_notes' => ['nullable', 'string', 'max:5000'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        if ($validated['status'] === 'scheduled' && empty($validated['scheduled_at'])) {
            return back()->with('error', 'Please provide a scheduled date and time.');
        }

        $shaafiRequest->update([
            'status' => $validated['status'],
            'agent_notes' => $validated['agent_notes'] ?? $shaafiRequest->agent_notes,
            'scheduled_at' => $validated['scheduled_at'] ?? $shaafiRequest->scheduled_at,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('shaafi-requests.show', $shaafiRequest)
            ->with('success', 'Request updated successfully.');
    }

    public function approve(ShaafiRequest $shaafiRequest)
    {
        $this->authorizeRequest($shaafiRequest);

        $shaafiRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('shaafi-requests.show', $shaafiRequest)
            ->with('success', 'Donor/request approved successfully.');
    }

    public function reject(Request $request, ShaafiRequest $shaafiRequest)
    {
        $this->authorizeRequest($shaafiRequest);

        $validated = $request->validate([
            'agent_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $shaafiRequest->update([
            'status' => 'rejected',
            'agent_notes' => $validated['agent_notes'] ?? $shaafiRequest->agent_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('shaafi-requests.show', $shaafiRequest)
            ->with('success', 'Request rejected.');
    }

    private function scopedQuery()
    {
        $user = auth()->user();
        $query = ShaafiRequest::query();

        if ($user->isSuperAdmin()) {
            $tenantId = (int) session('active_hospital_id', 0);
            if ($tenantId > 0) {
                $query->where('hospital_id', $tenantId);
            }

            return $query;
        }

        // Hospital staff see their hospital + all requests in the same city (case-insensitive).
        if ($user->hospital_id) {
            $hospital = Hospital::find($user->hospital_id);

            $query->where(function ($q) use ($user, $hospital) {
                $q->where('hospital_id', $user->hospital_id);

                if ($hospital?->city) {
                    $q->orWhereRaw('LOWER(city) = ?', [strtolower($hospital->city)]);
                }
            });
        }

        return $query;
    }

    private function userCanAccessRequest(ShaafiRequest $shaafiRequest): bool
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $tenantId = (int) session('active_hospital_id', 0);

            return $tenantId <= 0 || $shaafiRequest->hospital_id === $tenantId;
        }

        if (! $user->hospital_id) {
            return true;
        }

        if ($shaafiRequest->hospital_id === $user->hospital_id) {
            return true;
        }

        $hospital = Hospital::find($user->hospital_id);

        return $hospital?->city
            && strtolower($shaafiRequest->city) === strtolower($hospital->city);
    }

    private function authorizeRequest(ShaafiRequest $shaafiRequest): void
    {
        if (! $this->userCanAccessRequest($shaafiRequest)) {
            abort(403);
        }
    }

    private function scopeMeta(): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $tenantId = (int) session('active_hospital_id', 0);
            if ($tenantId > 0) {
                $hospital = Hospital::find($tenantId);

                return [
                    'label' => 'Super Admin view',
                    'detail' => 'Filtered to: ' . ($hospital?->name ?? "Hospital #{$tenantId}"),
                    'can_clear_tenant' => true,
                ];
            }

            return [
                'label' => 'Super Admin view',
                'detail' => 'Showing all hospitals',
                'can_clear_tenant' => false,
            ];
        }

        if ($user->hospital_id) {
            $hospital = $user->hospital ?? Hospital::find($user->hospital_id);

            return [
                'label' => 'Hospital view',
                'detail' => 'Showing: ' . ($hospital?->name ?? "Hospital #{$user->hospital_id}")
                    . ($hospital?->city ? ' and all requests in ' . $hospital->city : ''),
                'can_clear_tenant' => false,
            ];
        }

        return [
            'label' => 'System view',
            'detail' => 'Showing all hospitals',
            'can_clear_tenant' => false,
        ];
    }
}
