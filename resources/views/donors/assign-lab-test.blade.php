@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-[#A58B8B] px-6 py-4">
      <h2 class="text-2xl font-semibold text-white">
        Assign Lab Test for Donor #{{ str_pad($donor->id, 6, '0', STR_PAD_LEFT) }}
      </h2>
    </div>

    <div class="px-6 py-8 space-y-8">
      <!-- Donor Info -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Donor Information</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
          <div>
            <dt class="text-sm text-gray-500">Name</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $donor->first_name }} {{ $donor->last_name }}</dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">Blood Type</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $donor->blood_type ?? 'Not specified' }}</dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">Age</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $donor->age }}</dd>
          </div>
          <div>
            <dt class="text-sm text-gray-500">Contact</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $donor->tell }}</dd>
          </div>
        </dl>
      </div>

      <!-- Form -->
      <form action="{{ route('donors.store-lab-test', $donor) }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Test Type -->
          <div>
            <label for="test_type" class="block text-sm font-medium text-gray-700">Test Type</label>
            <select id="test_type" name="test_type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A58B8B] focus:ring-[#A58B8B]">
              <option value="hiv">HIV</option>
              <option value="hepatitis_b">Hepatitis B</option>
              <option value="hepatitis_c">Hepatitis C</option>
              <option value="syphilis">Syphilis</option>
            </select>
          </div>

          <!-- Test Date -->
          <div>
            <label for="test_date" class="block text-sm font-medium text-gray-700">Test Date</label>
            <input type="date" name="test_date" id="test_date"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A58B8B] focus:ring-[#A58B8B]"
                   value="{{ date('Y-m-d') }}">
          </div>

          <!-- Result -->
          <div>
            <label for="result" class="block text-sm font-medium text-gray-700">Result</label>
            <select id="result" name="result"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A58B8B] focus:ring-[#A58B8B]">
              <option value="positive">Positive</option>
              <option value="negative">Negative</option>
              <option value="inconclusive">Inconclusive</option>
            </select>
          </div>

          <!-- Notes -->
          <div class="sm:col-span-2">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea id="notes" name="notes" rows="4"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A58B8B] focus:ring-[#A58B8B]"
                      placeholder="Additional notes about the test..."></textarea>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
          <a href="{{ route('donors.index') }}"
             class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#A58B8B]">
            Cancel
          </a>
          <button type="submit"
                  class="px-4 py-2 bg-[#A58B8B] text-white rounded-md hover:bg-[#C9A7A7] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#A58B8B]">
            Save Test Results
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
