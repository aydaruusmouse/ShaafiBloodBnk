@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Transfusion Details #{{ $transfusion->id }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('transfusions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                    Back to List
                </a>
                <a href="{{ route('transfusions.edit', $transfusion->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Patient Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 bg-blue-600 sm:px-6">
                    <h3 class="text-lg font-medium text-white">Patient Information</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Patient Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('patients.show', $transfusion->patient_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $transfusion->patient->name }}
                                </a>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Blood Group</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transfusion->patient->blood_group }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Transfusion Details -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 bg-blue-600 sm:px-6">
                    <h3 class="text-lg font-medium text-white">Transfusion Details</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Blood Bag</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $transfusion->bloodBag->serial_number ?? 'N/A' }}
                                @if($transfusion->bloodBag)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $transfusion->bloodBag->blood_group }}
                                    </span>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst(str_replace('_', ' ', $transfusion->bloodBag->component_type)) }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Transfusion Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $transfusion->transfusion_date->format('M d, Y H:i') }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $transfusion->reason }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-4 py-5 bg-gray-50 sm:px-6">
                <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transfusion->notes ?? 'No additional notes provided.' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Created At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transfusion->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $transfusion->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection