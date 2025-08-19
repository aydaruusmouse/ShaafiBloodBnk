@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
  <div class="bg-white rounded-2xl shadow overflow-hidden">

    {{-- Header: avatar + name + meta + actions --}}
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <div class="flex items-center space-x-6">
        <img src="{{ asset('icons/user-avatar.svg') }}" alt="Avatar"
             class="w-20 h-20 rounded-full object-cover bg-gray-100">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ $donor->first_name }} {{ $donor->last_name }}
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            Age: {{ $donor->age }} · Blood: {{ $donor->blood_group ?? 'N/A' }} ·
            Last donated: {{ optional($donor->last_donation_at)->diffForHumans() ?? 'Never' }}
          </p>
        </div>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('donors.edit', $donor) }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
          Edit
        </a>
        <a href="{{ route('donors.index') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
          Back
        </a>
      </div>
    </div>

    {{-- Body: divided sections --}}
    <div class="divide-y divide-gray-200">

      {{-- Personal Information --}}
      <section class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <span class="block text-xs text-gray-500">Full Name</span>
            <span class="block text-sm text-gray-900">{{ $donor->first_name }} {{ $donor->last_name }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Date of Birth</span>
            <span class="block text-sm text-gray-900">{{ $donor->date_of_birth->format('M d, Y') }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Sex</span>
            <span class="block text-sm text-gray-900">{{ ucfirst($donor->sex) }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Age</span>
            <span class="block text-sm text-gray-900">{{ $donor->age }}</span>
          </div>
        </div>
      </section>

      {{-- Contact & Location --}}
      <section class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Contact & Location</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <span class="block text-xs text-gray-500">Phone</span>
            <span class="block text-sm text-gray-900">{{ $donor->tell }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Village</span>
            <span class="block text-sm text-gray-900">{{ $donor->village }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Occupation</span>
            <span class="block text-sm text-gray-900">{{ $donor->occupation }}</span>
          </div>
        </div>
      </section>

      {{-- Health Information --}}
      <section class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Health Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <span class="block text-xs text-gray-500">Weight</span>
            <span class="block text-sm text-gray-900">{{ $donor->weight }} kg</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Blood Pressure</span>
            <span class="block text-sm text-gray-900">{{ $donor->bp }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Hemoglobin</span>
            <span class="block text-sm text-gray-900">{{ $donor->hemoglobin }} g/dL</span>
          </div>
        </div>
      </section>

      {{-- Donation Details --}}
      <section class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <span class="block text-xs text-gray-500">Type of Donation</span>
            <span class="block text-sm text-gray-900">{{ $donor->type_of_donation ?? '—' }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Blood Group</span>
            <span class="block text-sm text-gray-900">{{ $donor->blood_group ?? '—' }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Screening</span>
            <span class="block text-sm text-gray-900">{{ $donor->screening ?? '—' }}</span>
          </div>
          <div>
            <span class="block text-xs text-gray-500">Eligible</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                 {{ $donor->is_eligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              {{ $donor->is_eligible ? 'Yes' : 'No' }}
            </span>
          </div>
        </div>
      </section>

    </div>

  </div>
</div>
@endsection
