{{-- resources/views/donors/lab-results/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  <h2 class="text-2xl font-semibold text-gray-900 mb-4">Donors with Lab Results</h2>


  <form method="GET" action="{{ route('donors.lab-results.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <!-- name & phone inputs… -->
  </form>




  <div class="bg-white rounded-lg overflow-hidden shadow">
    <div class="overflow-x-auto">
    <div class="overflow-x-auto">
  <table class="table-fixed border-collapse w-full">
    <thead class="bg-gray-50">
      <tr>
        {{-- we assign each <th> a fixed fraction of the total width that sums to 100% --}}
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Donor #
        </th>
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Phone
        </th>
        <th scope="col" class="w-2/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Name
        </th>
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Blood Group
        </th>
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Status
        </th>
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Bag
        </th>
        <th scope="col" class="w-1/6 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
          Action
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 bg-white">
      @forelse($donors as $donor)
      <tr class="hover:bg-gray-50">
        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
          #{{ str_pad($donor->id, 6, '0', STR_PAD_LEFT) }}
        </td>
        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
          {{ $donor->tell }}
        </td>
        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
          {{ $donor->first_name }} {{ $donor->last_name }}
        </td>
        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
          {{ $donor->blood_group ?: 'Not Set' }}
        </td>
        <td class="px-4 py-3 text-sm whitespace-nowrap">
          <span class="px-2 inline-flex text-xs font-semibold rounded-full
            {{ $donor->status === 'Legible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $donor->status ?: 'N/A' }}
          </span>
        </td>
        <td class="px-4 py-3 text-sm whitespace-nowrap">
          @if($donor->status === 'Legible')
            <a href="{{ route('blood-bags.create', ['donor' => $donor->id]) }}"
               class="inline-block px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600">
              Assign Bag
            </a>
          @else
            <span class="text-gray-400 text-xs">Not legible</span>
          @endif
        </td>
        <td class="px-4 py-3 text-sm whitespace-nowrap">
          <a href="{{ route('donors.lab-results.show', $donor) }}"
             class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
            <img src="{{ asset('icons/eye-line.png') }}"
                 alt="View"
                 class="w-5 h-5 inline-block">
          </a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
          No donors with lab results found.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

</div>

  </div>

  <div class="mt-4 flex justify-center">
    {{ $donors->links() }}
  </div>
</div>
@endsection