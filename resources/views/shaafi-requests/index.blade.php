@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Shaafi App Requests</h1>
                <p class="mt-1 text-sm text-gray-500">External donation and blood request submissions from Shaafi App</p>
            </div>
            @if($pendingCount > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    {{ $pendingCount }} pending
                </span>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
            <strong>{{ $scopeMeta['label'] }}:</strong> {{ $scopeMeta['detail'] }}.
            @if($totalInDatabase > 0 && $requests->total() === 0)
                <span class="block mt-1 text-blue-900">
                    There are {{ $totalInDatabase }} request(s) in the system, but none match your current access scope or filters.
                    @if($scopeMeta['can_clear_tenant'])
                        <form action="{{ route('super-admin.clear-tenant-context') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="underline font-medium">Clear hospital filter</button>
                        </form>
                        to see all.
                    @elseif(auth()->user()->hospital_id)
                        Your account is linked to a different hospital than these requests (they are for <strong>Hargeisa Hospital</strong>).
                    @endif
                </span>
            @endif
        </div>

        <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
            <form method="GET" action="{{ route('shaafi-requests.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="SR-..., name, phone"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="request_type" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">All types</option>
                        <option value="donation" @selected(request('request_type') === 'donation')>Donation</option>
                        <option value="blood_request" @selected(request('request_type') === 'blood_request')>Blood Request</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">All statuses</option>
                        @foreach(['pending','under_review','approved','rejected','scheduled','completed','cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">City</label>
                    <select name="city" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">All cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Hospital</label>
                    <select name="hospital_id" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">All hospitals</option>
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}" @selected((string) request('hospital_id') === (string) $hospital->id)>
                                {{ $hospital->name }} ({{ $hospital->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Filter</button>
                    <a href="{{ route('shaafi-requests.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Blood</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hospital</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $request->reference_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->request_type_label }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $request->full_name }}
                            <div class="text-xs text-gray-400">{{ $request->mobile_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $request->blood_group }}
                            @if($request->blood_quantity)
                                <span class="text-xs text-gray-400">({{ $request->blood_quantity }} bag{{ $request->blood_quantity > 1 ? 's' : '' }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $request->hospital->name }}
                            <div class="text-xs text-gray-400">{{ $request->city }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $request->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('shaafi-requests.show', $request) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 rounded-md text-xs font-medium hover:bg-blue-50">
                                    Review
                                </a>
                                @if(in_array($request->status, ['pending', 'under_review']))
                                <form action="{{ route('shaafi-requests.approve', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-md text-xs font-medium hover:bg-green-700"
                                        onclick="return confirm('Approve {{ $request->reference_number }}?')">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('shaafi-requests.reject', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md text-xs font-medium hover:bg-red-700"
                                        onclick="return confirm('Reject {{ $request->reference_number }}?')">
                                        Reject
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                            @if(request()->hasAny(['search', 'request_type', 'status', 'city', 'hospital_id']))
                                No requests match your filters.
                                <a href="{{ route('shaafi-requests.index') }}" class="text-blue-600 hover:underline">Clear filters</a>
                            @else
                                No Shaafi App requests found yet.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
