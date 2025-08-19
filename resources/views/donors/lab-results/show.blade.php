@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold text-gray-900">
      Lab Results for #{{ str_pad($donor->id,6,'0',STR_PAD_LEFT) }} — {{ $donor->first_name }} {{ $donor->last_name }}
    </h2>
    <a href="{{ route('donors.lab-results.index') }}"
       class="inline-flex items-center px-4 py-2 bg-green-400 hover:bg-green-500 text-white rounded-lg text-sm font-medium">
      ← Back
    </a>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="table-fixed w-full border-collapse">
      <colgroup>
        <col class="w-2/12"><!-- Test Date -->
        <col class="w-1/12"><!-- HIV -->
        <col class="w-1/12"><!-- Hep B -->
        <col class="w-1/12"><!-- Hep C -->
        <col class="w-1/12"><!-- Syphilis -->
        <col class="w-5/12"><!-- Status -->
      </colgroup>
      <thead class="bg-gray-50">
        <tr>
          @foreach(['Test Date','HIV','Hep B','Hep C','Syphilis','Status'] as $col)
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
              {{ $col }}
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($allTests as $test)
          @php
            $isEligible =
              $test->hiv === 'negative' &&
              $test->hepatitis_b === 'negative' &&
              $test->hepatitis_c === 'negative' &&
              $test->syphilis === 'negative';
          @endphp
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
              {{ $test->test_date->format('M d, Y') }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 text-xs font-medium rounded-full {{ $test->hiv==='negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($test->hiv) }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 text-xs font-medium rounded-full {{ $test->hepatitis_b==='negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($test->hepatitis_b) }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 text-xs font-medium rounded-full {{ $test->hepatitis_c==='negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($test->hepatitis_c) }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 text-xs font-medium rounded-full {{ $test->syphilis==='negative' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($test->syphilis) }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <span class="px-2 text-xs font-medium rounded-full {{ $isEligible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $isEligible ? 'Eligible' : 'Not Eligible' }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
              No lab results for this donor.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Back to list --}}
  <div>
    <a href="{{ route('donors.lab-results.index') }}"
       class="text-sm text-gray-600 hover:underline">
      ← Back to list
    </a>
  </div>

</div>
@endsection
