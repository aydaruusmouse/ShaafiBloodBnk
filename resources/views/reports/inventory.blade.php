@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Inventory Report</h1>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Reports
            </a>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow-sm rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500">Total Units</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $summary['total_units'] }}</p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500">Available</p>
                <p class="text-2xl font-semibold text-green-600">{{ $summary['available'] }}</p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4">
                <p class="text-sm font-medium text-gray-500">Expired</p>
                <p class="text-2xl font-semibold text-red-600">{{ $summary['expired'] }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <form action="{{ route('reports.inventory') }}" method="GET" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                        <select name="blood_group" id="blood_group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($bloodGroups as $bg)
                                <option value="{{ $bg }}" {{ request('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="expiring_within_days" class="block text-sm font-medium text-gray-700">Expiring within (days)</label>
                        <select name="expiring_within_days" id="expiring_within_days" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">—</option>
                            <option value="7" {{ request('expiring_within_days') == '7' ? 'selected' : '' }}>7 days</option>
                            <option value="14" {{ request('expiring_within_days') == '14' ? 'selected' : '' }}>14 days</option>
                            <option value="30" {{ request('expiring_within_days') == '30' ? 'selected' : '' }}>30 days</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700">Apply</button>
                        <a href="{{ route('reports.inventory') }}" class="ml-2 px-4 py-2 bg-gray-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->batch_number ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->blood_group ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->donor)
                                        {{ $item->donor->first_name }} {{ $item->donor->last_name }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $item->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status === 'used' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $item->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ !in_array($item->status ?? '', ['available','used','expired']) ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ $item->status ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->collection_date ? $item->collection_date->format('Y-m-d') : '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $item->expiry_date && $item->expiry_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                    {{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->storageLocation?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-sm text-gray-500 text-center">No inventory items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
