@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

  <!-- Heading -->
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-gray-900">Registered Donors <span class="text-gray-400 text-base font-normal">({{ number_format($donors->total()) }})</span></h2>
    @auth
      @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'hospital_admin', 'staff', 'reception']))
        <a href="{{ route('donors.create') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <i class="ri-add-line mr-1 text-base"></i>
          Add Donor
        </a>
      @endif
    @endauth
  </div>

  <!-- Search Bar -->
  <div class="mb-6">
    <form action="{{ route('donors.search') }}" method="GET" x-data="{ q: '{{ request('query') }}' }">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
        <input
          type="text"
          name="query"
          x-model.debounce.500ms="q"
          @input="$event.target.form.submit()"
          value="{{ request('query') }}"
          placeholder="Search for a donor…"
          class="block w-full pl-10 pr-20 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
          aria-label="Search donors"
        />
        <div class="absolute inset-y-0 right-0 pr-2 flex items-center space-x-1">
          <button type="button" x-show="q" @click="q=''; $nextTick(() => $el.closest('form').submit());" class="px-2 py-1 text-xs text-gray-600 hover:text-gray-900">
            Clear
          </button>
          <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
            <i class="ri-search-line mr-1"></i>
            Search
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Flash Message -->
  @if(session('success'))
    <div
      x-data="{ show: true }"
      x-init="setTimeout(() => show = false, 3000)"
      x-show="show"
      x-transition
      class="mb-6"
    >
      <div class="flex items-center bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
        <svg class="h-5 w-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2l4-4"/>
        </svg>
        <span class="text-sm">{{ session('success') }}</span>
      </div>
    </div>
  @endif

  @if(session('error'))
    <div
      x-data="{ show: true }"
      x-init="setTimeout(() => show = false, 3000)"
      x-show="show"
      x-transition
      class="mb-6"
    >
      <div class="flex items-center bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-lg">
        <svg class="h-5 w-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm">{{ session('error') }}</span>
      </div>
    </div>
  @endif

  <!-- Table Container -->
  <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full table-auto divide-y divide-gray-200">
        <thead class="bg-gray-50 sticky top-0 z-10">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Donor Number</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tel</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Blood Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Donor Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @if(!auth()->check())
            <script>window.location = '{{ route('login') }}';</script>
          @endif
          @forelse($donors as $donor)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
              {{ str_pad($donor->id, 6, '0', STR_PAD_LEFT) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
              {{ $donor->tell }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                <i class="ri-drop-fill mr-1 text-blue-500"></i>
                {{ $donor->blood_group ?? '–' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ $donor->first_name }} {{ $donor->last_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              @php $s = $donor->status; @endphp
              @if($s === 'Legible')
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Legible</span>
              @elseif($s === 'Illegible')
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Ineligible</span>
              @else
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
              @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <div class="flex items-center space-x-2">
                @auth
                  @php
                    $userRole = auth()->user()->role ? auth()->user()->role->name : null;
                    $canEdit = in_array($userRole, ['admin', 'hospital_admin', 'staff', 'reception']);
                    $canDelete = in_array($userRole, ['admin', 'hospital_admin']);
                    $canLabTest = in_array($userRole, ['admin', 'hospital_admin', 'lab', 'staff']);
                  @endphp
                  
                  <!-- View Button - All authenticated users -->
                  <a href="{{ route('donors.show', $donor) }}"
                     class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                     title="View Details">
                    <i class="ri-eye-line text-base"></i>
                  </a>

                  <!-- Lab Test Button - Admin, Hospital Admin, Lab, Staff -->
                  @if($canLabTest)
                    <a href="{{ route('donors.lab-test.select', $donor) }}"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-800 transition-colors"
                       title="Assign Lab Test">
                      <i class="ri-test-tube-line text-base"></i>
                    </a>
                  @endif

                  <!-- Edit Button - Admin, Hospital Admin, Staff, Reception -->
                  @if($canEdit)
                    <a href="{{ route('donors.edit', $donor) }}"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800 transition-colors"
                       title="Edit Donor">
                      <i class="ri-edit-line text-base"></i>
                    </a>
                  @endif

                  <!-- Delete Button - Admin, Hospital Admin only -->
                  @if($canDelete)
                    <form action="{{ route('donors.destroy', $donor) }}"
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this donor? This action cannot be undone.')"
                          class="inline">
                      @csrf 
                      @method('DELETE')
                      <button type="submit"
                              class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 transition-colors"
                              title="Delete Donor">
                        <i class="ri-delete-bin-6-line text-base"></i>
                      </button>
                    </form>
                  @endif
                @endauth
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
              <div class="flex flex-col items-center justify-center">
                <i class="ri-user-search-line text-2xl text-gray-400 mb-2"></i>
                <span>No donors found.</span>
                @if(request('query'))
                  <span class="text-xs text-gray-400 mt-1">Try adjusting your search terms.</span>
                @endif
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <div class="mt-6">
    {{ $donors->links() }}
  </div>

  <!-- Quick Actions -->
  @auth
    @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'hospital_admin', 'lab']))
      <div class="mt-8 bg-gray-50 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('donors.lab-results.index') }}" 
             class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
            <i class="ri-file-list-3-line mr-2"></i>
            View Lab Results
          </a>
          <a href="{{ route('donors.with-lab-results') }}" 
             class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
            <i class="ri-test-tube-line mr-2"></i>
            Donors with Lab Results
          </a>
        </div>
      </div>
    @endif
  @endauth
</div>
@endsection