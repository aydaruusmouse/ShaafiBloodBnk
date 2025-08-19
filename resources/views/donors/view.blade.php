{{-- @extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Donor Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Personal Information</h2>
                <ul class="mt-2 space-y-2">
                    <li><span class="font-medium">Name:</span> {{ $donor->first_name }} {{ $donor->last_name }}</li>
                    <li><span class="font-medium">Date of Birth:</span> {{ $donor->date_of_birth->format('M d, Y') }}</li>
                    <li><span class="font-medium">Sex:</span> {{ ucfirst($donor->sex) }}</li>
                    <li><span class="font-medium">Age:</span> {{ $donor->age }}</li>
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Contact & Location</h2>
                <ul class="mt-2 space-y-2">
                    <li><span class="font-medium">Phone:</span> {{ $donor->tell }}</li>
                    <li><span class="font-medium">Village:</span> {{ $donor->village }}</li>
                    <li><span class="font-medium">Occupation:</span> {{ $donor->occupation }}</li>
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Health Information</h2>
                <ul class="mt-2 space-y-2">
                    <li><span class="font-medium">Weight:</span> {{ $donor->weight }} kg</li>
                    <li><span class="font-medium">Blood Pressure:</span> {{ $donor->bp }}</li>
                    <li><span class="font-medium">Hemoglobin:</span> {{ $donor->hemoglobin }} g/dL</li>
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-700">Donation Details</h2>
                <ul class="mt-2 space-y-2">
                    <li><span class="font-medium">Type of Donation:</span> {{ $donor->type_of_donation }}</li>
                    <li><span class="font-medium">Blood Group:</span> {{ $donor->blood_group }}</li>
                    <li><span class="font-medium">Screening:</span> {{ $donor->screening }}</li>
                    <li><span class="font-medium">Eligible:</span> {{ $donor->is_eligible ? 'Yes' : 'No' }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('donors.index') }}" 
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Back</a>
            <a href="{{ route('donors.edit', $donor) }}" 
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Edit</a>
        </div>
    </div>
</div>
@endsection --}}
