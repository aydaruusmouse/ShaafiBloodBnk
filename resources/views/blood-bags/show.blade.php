@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                🩸 Blood Bag Details
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Serial Number: {{ $bloodBag->serial_number }}
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('blood-bags.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Inventory
            </a>
            <a href="{{ route('blood-bags.edit', ['donor' => $donor->id, 'bloodBag' => $bloodBag->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Bag
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-4">
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Donor</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->donor->name }}</dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Blood Group</dt>
                        <dd class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $bloodBag->blood_group }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Component Type</dt>
                        <dd class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($bloodBag->component_type) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Volume</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->volume }} ml</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Status Information Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Status Information</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-4">
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Status</dt>
                        <dd class="text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $bloodBag->status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($bloodBag->status === 'reserved' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($bloodBag->status === 'transfused' ? 'bg-blue-100 text-blue-800' : 
                                   'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($bloodBag->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Collection Date</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->collection_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Expiry Date</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->expiry_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Collected By</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->collected_by }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden md:col-span-2">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Additional Information</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-4">
                    @if($bloodBag->patient)
                    <div class="flex items-center">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Patient</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->patient->name }}</dd>
                    </div>
                    @endif
                    @if($bloodBag->notes)
                    <div class="flex items-start">
                        <dt class="text-sm font-medium text-gray-500 w-1/3">Notes</dt>
                        <dd class="text-sm text-gray-900">{{ $bloodBag->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <!-- Delete Button -->
    <div class="mt-6 flex justify-end">
        <form action="{{ route('blood-bags.destroy', ['donor' => $donor->id, 'bloodBag' => $bloodBag->id]) }}" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this blood bag?');">
            @csrf 
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Blood Bag
            </button>
        </form>
    </div>
</div>
@endsection
