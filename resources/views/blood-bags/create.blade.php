@extends('layouts.app')

@section('content')
<div 
  class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8"
  x-data="{ 
    donorType: '{{ old('donor_type', 'volunteer') }}', 
    showPatientForm: false 
  }"
  x-init="$watch('donorType', value => showPatientForm = (value === 'family_replacement'))"
>

  {{-- Header --}}
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Add Bag for {{ $donor->name }}</h1>
    <a href="{{ route('blood-bags.index') }}"
       class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md px-4 py-2">
      Back to inventory
    </a>
  </div>

  {{-- Error Message --}}
  @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
      <span class="block sm:inline">{{ session('error') }}</span>
    </div>
  @endif

  <p class="text-gray-600 mb-6 text-sm">Add new bag</p>

  <form action="{{ route('blood-bags.store', ['donor' => $donor->id]) }}" method="POST" class="space-y-6">
    @csrf

    {{-- Bag Serial Number + Volume --}}
    <div class="flex flex-wrap gap-4">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Bag SS/N <span class="text-red-500">*</span></label>
        <input type="text" name="serial_number" value="{{ old('serial_number') }}" required
               class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm focus:ring-green-500 focus:border-green-500">
        @error('serial_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Volume (ml) <span class="text-red-500">*</span></label>
        <input type="number" name="volume" min="0" step="1" value="{{ old('volume') }}" required
               class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm focus:ring-green-500 focus:border-green-500">
        @error('volume')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Collection Date + Donor Type --}}
    <div class="flex flex-wrap gap-4">
      <div class="flex-1 min-w-[240px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Collection Date</label>
        <input type="date" name="collection_date"
               value="{{ old('collection_date', now()->format('Y-m-d')) }}" required
               class="h-10 w-full rounded-md border border-gray-300 px-3 text-sm">
        @error('collection_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="flex-1 min-w-[240px]">
        <span class="block text-sm font-medium text-gray-700 mb-1">Donor Type</span>
        <div class="flex space-x-6">
          <label class="inline-flex items-center text-sm">
            <input type="radio" name="donor_type" value="volunteer" x-model="donorType"
                   class="h-4 w-4 text-green-500 border-gray-300 rounded">
            <span class="ml-2">Volunteer</span>
          </label>
          <label class="inline-flex items-center text-sm">
            <input type="radio" name="donor_type" value="family_replacement" x-model="donorType"
                   class="h-4 w-4 text-green-500 border-gray-300 rounded">
            <span class="ml-2">Family</span>
          </label>
        </div>
        @error('donor_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Blood Components --}}
    <div>
      <span class="block text-sm font-medium text-gray-700 mb-1">Blood Component <span class="text-red-500">*</span></span>
      <div class="flex flex-col gap-2 pl-2">
        @foreach(['whole' => 'Whole', 'plasma' => 'Plasma', 'rbc' => 'RBC', 'platelets' => 'Platelets'] as $key => $label)
          <label class="inline-flex items-center text-sm">
            <input type="radio" name="components[]" value="{{ $key }}"
                   {{ old('components.0') == $key ? 'checked' : '' }}
                   class="h-4 w-4 text-green-500 border-gray-300 rounded">
            <span class="ml-2">{{ $label }}</span>
          </label>
        @endforeach
      </div>
      @error('components')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Patient Info Modal --}}
    <div x-show="showPatientForm" x-transition style="display: none;"
         class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-auto overflow-hidden">
        {{-- Close button --}}
        <button type="button" @click="showPatientForm = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl transition">
          &times;
        </button>

        {{-- Modal content --}}
        <div class="p-6 sm:p-10">
          <h2 class="text-3xl font-semibold text-center text-gray-800 mb-8">Patient Information</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Patient Name --}}
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Patient Name <span class="text-red-500">*</span></label>
              <input type="text" name="patient_name" :required="donorType === 'family_replacement'"
                     class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm" />
              @error('patient_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- MRN --}}
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Medical Record Number <span class="text-red-500">*</span></label>
              <input type="text" name="patient_mrn" :required="donorType === 'family_replacement'"
                     class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm" />
              @error('patient_mrn')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Phone --}}
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
              <input type="text" name="patient_phone" :required="donorType === 'family_replacement'"
                     class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm" />
              @error('patient_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Address --}}
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
              <input type="text" name="patient_address" :required="donorType === 'family_replacement'"
                     class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm" />
              @error('patient_address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Medical History --}}
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Medical History</label>
              <textarea name="patient_medical_history" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm text-sm resize-none"></textarea>
              @error('patient_medical_history')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Save Button --}}
          <div class="mt-8 flex justify-end">
            <button type="button" @click="showPatientForm = false"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors">
              Save Patient Info
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Hidden Fields --}}
    <input type="hidden" name="donor_id" value="{{ $donor->id }}">
    <input type="hidden" name="blood_group" value="{{ $donor->blood_group }}">
    <input type="hidden" name="status" value="available">
    <input type="hidden" name="collected_by" value="{{ auth()->user()->name }}">
    <input type="hidden" name="expiry_date" value="{{ now()->addDays(35)->format('Y-m-d') }}">

    <div class="flex justify-end">
      <button type="submit"
              class="bg-green-500 hover:bg-green-600 mt-4 w-full md:w-auto px-8 py-3 text-white text-sm font-medium rounded-md">
        Add to Inventory
      </button>
    </div>
  </form>
</div>
@endsection
