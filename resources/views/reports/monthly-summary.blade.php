@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Monthly Summary Report</h1>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm mb-8">
        <form action="{{ route('reports.monthly-summary') }}" method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="month_year" class="block text-sm font-medium text-gray-700">Month</label>
                    <select id="month_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($monthsForSelect as $value => $label)
                            @php
                                $parts = explode('-', $value);
                                $selected = (int)$parts[0] === (int)$year && (int)$parts[1] === (int)$monthNum;
                            @endphp
                            <option value="{{ $value }}" {{ $selected ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="year" id="year" value="{{ $year }}">
                    <input type="hidden" name="month" id="month" value="{{ $monthNum }}">
                </div>
                <div>
                    <label for="blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                    <select name="blood_group" id="blood_group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All blood groups</option>
                        @foreach($bloodGroupsForSelect as $bg)
                            <option value="{{ $bg }}" {{ $bloodGroup === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700">Apply</button>
                    <a href="{{ route('reports.monthly-summary') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary for selected month (same look as dashboard) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-3">
                        <i class="ri-user-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Donors</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $totalDonors }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold {{ $donorsIncreasePct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    <i class="ri-arrow-{{ $donorsIncreasePct >= 0 ? 'up' : 'down' }}-line"></i>
                                    <span class="sr-only">vs last month</span>
                                    {{ number_format($donorsIncreasePct, 1) }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3">
                        <i class="ri-check-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Eligible Donors</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">{{ $eligibleDonors }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold {{ $eligibleIncreasePct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    <i class="ri-arrow-{{ $eligibleIncreasePct >= 0 ? 'up' : 'down' }}-line"></i>
                                    <span class="sr-only">vs last month</span>
                                    {{ number_format($eligibleIncreasePct, 1) }}%
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3">
                        <i class="ri-heart-pulse-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $pendingRequests }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                        <i class="ri-drop-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available Units</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $availableUnits }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blood Group Distribution -->
    <div class="bg-white rounded-xl shadow-sm mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Blood Group Distribution</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($bloodGroupStats as $group => $count)
                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500">{{ $group }}</div>
                                <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $count }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ number_format(($count / $totalForPct) * 100, 1) }}%
                            </div>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / $totalForPct) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(empty($bloodGroupStats))
                <p class="text-sm text-gray-500 py-4">No donors in this period.</p>
            @endif
        </div>
    </div>

    <!-- Monthly Overview (same look as dashboard) -->
    @if(!empty($monthlyOverview))
    @php $month = $monthlyOverview[0]; @endphp
    <div class="mb-8">
        <div class="px-1 mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Monthly Overview <span class="font-mono text-indigo-600">({{ $month['code'] }})</span> — {{ $month['label'] }}</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-3">
                            <i class="ri-user-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Donors</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $month['total_donors'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3">
                            <i class="ri-check-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Eligible Donors</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $month['eligible_donors'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3">
                            <i class="ri-heart-pulse-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $month['pending_requests'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                            <i class="ri-drop-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Available Units</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $month['available_units'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Blood Group Distribution</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @forelse($month['blood_group_stats'] ?? [] as $group => $count)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-500">{{ $group }}</div>
                                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $count }}</div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $month['month_total'] > 0 ? number_format(($count / $month['month_total']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $month['month_total'] > 0 ? ($count / $month['month_total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 col-span-full py-4">No donors this month</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('month_year');
    if (sel) {
        sel.addEventListener('change', function() {
            var v = this.value;
            if (v) {
                var parts = v.split('-');
                document.getElementById('year').value = parts[0];
                document.getElementById('month').value = parts[1];
            }
        });
        var v = sel.value;
        if (v) {
            var parts = v.split('-');
            document.getElementById('year').value = parts[0];
            document.getElementById('month').value = parts[1];
        }
    }
});
</script>
@endpush
@endsection
