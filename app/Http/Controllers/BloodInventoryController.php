<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\BloodComponent;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BloodInventoryController extends Controller
{
    public function index()
    {
        // Set tenant context for hospital 3
        app()->instance('tenantId', 3);
        
        // Base query: only bags that have an assigned serial number
        $bagQuery = \App\Models\BloodBag::query()
            ->whereNotNull('serial_number')
            ->where('serial_number', '!=', '');

        // Paginated list with donor relation
        $bloodBags = (clone $bagQuery)
            ->with(['donor'])
            ->latest()
            ->paginate(10);

        // Summary stats from the same filtered set
        $totalUnits = (clone $bagQuery)->count();
        $availableUnits = (clone $bagQuery)->where('status', 'available')->count();
        $reservedUnits = (clone $bagQuery)->where('status', 'reserved')->count();
        $transfusedUnits = (clone $bagQuery)->where('status', 'transfused')->count();

        // Debug: Log the results
        \Log::info('BloodInventoryController - Found ' . $totalUnits . ' total units');
        \Log::info('BloodInventoryController - Available: ' . $availableUnits . ', Reserved: ' . $reservedUnits . ', Transfused: ' . $transfusedUnits);
        \Log::info('BloodInventoryController - Paginated bags count: ' . $bloodBags->count());

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $components = \App\Models\BloodComponent::all();
        $storageLocations = \App\Models\StorageLocation::all();

        return view('inventory.index', compact('bloodBags', 'totalUnits', 'availableUnits', 'reservedUnits', 'transfusedUnits', 'bloodGroups', 'components', 'storageLocations'));
    }

    public function create()
    {
        $components = BloodComponent::all();
        $storageLocations = StorageLocation::all();
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return view('inventory.create', compact('components', 'storageLocations', 'bloodGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blood_group' => 'required|string',
            'component_id' => 'required|exists:blood_components,id',
            'donor_id' => 'nullable|exists:donors,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'collection_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $component = BloodComponent::findOrFail($validated['component_id']);
        
        $inventory = BloodInventory::create([
            'batch_number' => 'BAT-' . Str::random(8),
            'blood_group' => $validated['blood_group'],
            'component_id' => $validated['component_id'],
            'donor_id' => $validated['donor_id'],
            'storage_location_id' => $validated['storage_location_id'],
            'status' => 'available',
            'collection_date' => $validated['collection_date'],
            'expiry_date' => now()->addDays($component->shelf_life_days),
            'barcode' => 'BC-' . Str::random(10),
            'notes' => $validated['notes'],
            'test_status' => 'pending'
        ]);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Blood unit added to inventory successfully.');
    }

    public function show(BloodInventory $inventory)
    {
        $inventory->load(['component', 'donor', 'storageLocation']);
        return view('inventory.show', compact('inventory'));
    }

    public function edit(BloodInventory $inventory)
    {
        $components = BloodComponent::all();
        $storageLocations = StorageLocation::all();
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return view('inventory.edit', compact('inventory', 'components', 'storageLocations', 'bloodGroups'));
    }

    public function update(Request $request, BloodInventory $inventory)
    {
        $validated = $request->validate([
            'blood_group' => 'required|string',
            'component_id' => 'required|exists:blood_components,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'status' => 'required|in:available,reserved,used,expired',
            'test_status' => 'required|in:pending,passed,failed',
            'test_date' => 'nullable|date',
            'tested_by' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Blood unit updated successfully.');
    }

    public function destroy(BloodInventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')
            ->with('success', 'Blood unit removed from inventory.');
    }

    public function expiryAlerts()
    {
        $expiringSoon = BloodInventory::where('status', 'available')
            ->where('expiry_date', '<=', now()->addDays(7))
            ->where('expiry_date', '>', now())
            ->with(['component', 'storageLocation'])
            ->get();

        $expired = BloodInventory::where('status', 'available')
            ->where('expiry_date', '<=', now())
            ->with(['component', 'storageLocation'])
            ->get();

        return view('inventory.expiry-alerts', compact('expiringSoon', 'expired'));
    }
}
