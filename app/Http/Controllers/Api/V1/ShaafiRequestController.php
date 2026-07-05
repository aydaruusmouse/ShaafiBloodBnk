<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShaafiRequestRequest;
use App\Models\Hospital;
use App\Models\ShaafiRequest;
use Illuminate\Http\JsonResponse;

class ShaafiRequestController extends Controller
{
    public function store(StoreShaafiRequestRequest $request): JsonResponse
    {
        if ($request->filled('external_reference')) {
            $existing = ShaafiRequest::where('external_reference', $request->external_reference)->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Request already submitted.',
                    'data' => $this->formatRequest($existing),
                ]);
            }
        }

        $hospital = Hospital::where('id', $request->hospital_id)
            ->where('status', 'active')
            ->where('city', $request->city)
            ->first();

        if (! $hospital) {
            return response()->json([
                'success' => false,
                'message' => 'The selected hospital is not available in the chosen city.',
            ], 422);
        }

        $shaafiRequest = ShaafiRequest::create([
            'request_type' => $request->request_type,
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'blood_group' => $request->blood_group,
            'blood_quantity' => $request->request_type === 'blood_request' ? $request->blood_quantity : null,
            'city' => $request->city,
            'hospital_id' => $hospital->id,
            'additional_notes' => $request->additional_notes,
            'shaafi_user_id' => $request->shaafi_user_id,
            'external_reference' => $request->external_reference,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully.',
            'data' => $this->formatRequest($shaafiRequest),
        ], 201);
    }

    private function formatRequest(ShaafiRequest $request): array
    {
        $request->loadMissing('hospital:id,name,city');

        return [
            'reference_number' => $request->reference_number,
            'request_type' => $request->request_type,
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'blood_group' => $request->blood_group,
            'blood_quantity' => $request->blood_quantity,
            'city' => $request->city,
            'hospital' => [
                'id' => $request->hospital->id,
                'name' => $request->hospital->name,
            ],
            'additional_notes' => $request->additional_notes,
            'status' => $request->status,
            'submitted_at' => $request->created_at?->toIso8601String(),
        ];
    }
}
