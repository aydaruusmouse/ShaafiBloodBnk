@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Blood Request Details</h1>
        <a href="{{ route('blood-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Back to List</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Hospital</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->hospital->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Department</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->department->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Patient</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->patient->name ?? $bloodRequest->patient_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Blood Group</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->blood_group }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Units Required</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->units_required ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Urgency</dt>
                <dd class="mt-1 text-lg text-gray-900">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $bloodRequest->urgency === 'high' ? 'bg-red-100 text-red-800' : ($bloodRequest->urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($bloodRequest->urgency) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Required Date</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ $bloodRequest->required_date ? $bloodRequest->required_date->format('Y-m-d') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($bloodRequest->status) }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-gray-900">{{ $bloodRequest->notes ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Available Blood Bags</h2>
        @if($availableBags->count())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serial</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Component</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($availableBags as $bag)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $bag->serial_number }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $bag->component_type)) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $bag->volume }} ml</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $bag->expiry_date ? $bag->expiry_date->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $bag->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($bag->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-gray-500">No available blood bags for this blood group and component type.</div>
        @endif
    </div>
</div>
@endsection 