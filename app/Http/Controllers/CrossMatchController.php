<?php

namespace App\Http\Controllers;

use App\Models\CrossMatch;
use App\Models\BloodRequest;
use App\Models\BloodBag;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrossMatchController extends Controller
{
    public function index()
    {
        $crossMatches = CrossMatch::with(['bloodRequest', 'bloodBag', 'patient'])
            ->latest()
            ->paginate(10);
        return view('cross-matches.index', compact('crossMatches'));
    }

    public function create(BloodRequest $bloodRequest)
    {
        $availableBags = BloodBag::where('blood_group', $bloodRequest->blood_group)
            ->where('component_type', $bloodRequest->component_type)
            ->where('status', 'available')
            ->get();
        return view('cross-matches.create', compact('bloodRequest', 'availableBags'));
    }

    public function store(Request $request, BloodRequest $bloodRequest)
    {
        $validated = $request->validate([
            'blood_bag_id' => 'required|exists:blood_bags,id',
            'is_compatible' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['blood_request_id'] = $bloodRequest->id;
        $validated['patient_id'] = $bloodRequest->patient_id;
        $validated['performed_by'] = Auth::user()->name;
        $validated['performed_at'] = now();

        $crossMatch = CrossMatch::create($validated);

        // Update blood bag status if compatible
        if ($validated['is_compatible']) {
            $bloodBag = BloodBag::find($validated['blood_bag_id']);
            $bloodBag->update(['status' => 'reserved']);
        }

        return redirect()->route('cross-matches.show', $crossMatch)
            ->with('success', 'Cross-match test completed successfully.');
    }

    public function show(CrossMatch $crossMatch)
    {
        $crossMatch->load(['bloodRequest', 'bloodBag', 'patient']);
        return view('cross-matches.show', compact('crossMatch'));
    }

    public function edit(CrossMatch $crossMatch)
    {
        $availableBags = BloodBag::where('blood_group', $crossMatch->bloodRequest->blood_group)
            ->where('component_type', $crossMatch->bloodRequest->component_type)
            ->where('status', 'available')
            ->get();
        return view('cross-matches.edit', compact('crossMatch', 'availableBags'));
    }

    public function update(Request $request, CrossMatch $crossMatch)
    {
        $validated = $request->validate([
            'blood_bag_id' => 'required|exists:blood_bags,id',
            'is_compatible' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        // If changing blood bag, update status of old and new bags
        if ($crossMatch->blood_bag_id != $validated['blood_bag_id']) {
            $oldBag = BloodBag::find($crossMatch->blood_bag_id);
            $oldBag->update(['status' => 'available']);

            $newBag = BloodBag::find($validated['blood_bag_id']);
            if ($validated['is_compatible']) {
                $newBag->update(['status' => 'reserved']);
            }
        } else {
            // If same bag but changing compatibility
            $bloodBag = BloodBag::find($validated['blood_bag_id']);
            $bloodBag->update(['status' => $validated['is_compatible'] ? 'reserved' : 'available']);
        }

        $crossMatch->update($validated);

        return redirect()->route('cross-matches.show', $crossMatch)
            ->with('success', 'Cross-match test updated successfully.');
    }

    public function destroy(CrossMatch $crossMatch)
    {
        // Update blood bag status back to available
        $bloodBag = BloodBag::find($crossMatch->blood_bag_id);
        $bloodBag->update(['status' => 'available']);

        $crossMatch->delete();
        return redirect()->route('cross-matches.index')
            ->with('success', 'Cross-match test deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $crossMatches = CrossMatch::whereHas('bloodRequest', function ($q) use ($query) {
                $q->whereHas('patient', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('patient_id', 'like', "%{$query}%");
                });
            })
            ->orWhereHas('bloodBag', function ($q) use ($query) {
                $q->where('serial_number', 'like', "%{$query}%");
            })
            ->paginate(10);

        return view('cross-matches.index', compact('crossMatches'));
    }
}
