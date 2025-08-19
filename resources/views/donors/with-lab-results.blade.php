@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
	<!-- Heading -->
	<div class="flex items-center justify-between mb-6">
		<h2 class="text-2xl font-semibold text-gray-900">Donors with Lab Results <span class="text-gray-400 text-base font-normal">({{ number_format($donors->total()) }})</span></h2>
		<a href="{{ route('donors.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
			<i class="ri-arrow-left-line mr-1"></i> Back to All Donors
		</a>
	</div>

	<!-- Table Container -->
	<div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-xl overflow-hidden">
		<div class="overflow-x-auto">
			<table class="w-full table-auto divide-y divide-gray-200">
				<thead class="bg-gray-50 sticky top-0 z-10">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Donor Number</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Phone</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Blood Group</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Donor Name</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Bag</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-100">
					@foreach($donors as $donor)
					@php $s = $donor->status; @endphp
					<tr class="hover:bg-gray-50">
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ str_pad($donor->id, 6, '0', STR_PAD_LEFT) }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $donor->tell }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							<span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
								<i class="ri-drop-fill mr-1 text-blue-500"></i>
								{{ $donor->blood_group ?? '–' }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $donor->first_name }} {{ $donor->last_name }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							@if($s === 'Legible')
								<span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Legible</span>
							@elseif($s === 'Illegible')
								<span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Illegible</span>
							@else
								<span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
							@endif
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							@if($s === 'Legible')
								<a href="{{ route('blood-bags.create', $donor) }}" class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 ring-1 ring-green-200 hover:bg-green-100">
									<i class="ri-add-line mr-1"></i> Assign Bag
								</a>
							@else
								<span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-gray-600 ring-1 ring-gray-200">Not legible</span>
							@endif
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							<div class="flex items-center space-x-3">
								@auth
									@if(auth()->user()->role_id == 1 || auth()->user()->role_id == 9) {{-- Admin or Hospital Admin --}}
										<a href="{{ route('donors.show', $donor) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900" title="View">
											<i class="ri-eye-line text-base"></i>
										</a>
										<a href="{{ route('donors.edit', $donor) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-indigo-50 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-800" title="Edit">
											<i class="ri-edit-line text-base"></i>
										</a>
										<form action="{{ route('donors.destroy', $donor) }}" method="POST" onsubmit="return confirm('Are you sure?')">
											@csrf @method('DELETE')
											<button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800" title="Delete">
												<i class="ri-delete-bin-6-line text-base"></i>
											</button>
										</form>
									@else {{-- Other roles - basic view access --}}
										<a href="{{ route('donors.show', $donor) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900" title="View">
											<i class="ri-eye-line text-base"></i>
										</a>
									@endif
								@endauth
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

	<!-- Pagination -->
	<div class="mt-6">
		{{ $donors->links() }}
	</div>
</div>
@endsection 