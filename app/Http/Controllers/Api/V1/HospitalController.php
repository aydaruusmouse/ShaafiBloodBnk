<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
        ]);

        $hospitals = Hospital::query()
            ->where('status', 'active')
            ->where('city', $validated['city'])
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'city', 'phone', 'email']);

        return response()->json([
            'success' => true,
            'data' => $hospitals->map(fn (Hospital $hospital) => [
                'id' => $hospital->id,
                'name' => $hospital->name,
                'address' => $hospital->address,
                'city' => $hospital->city,
                'phone' => $hospital->phone,
                'email' => $hospital->email,
            ]),
        ]);
    }
}
