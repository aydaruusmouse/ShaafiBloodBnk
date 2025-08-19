@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section with Black Background -->
        @if(!auth()->check())
            <script>window.location = '{{ route('login') }}';</script>
        @endif
        <div class="bg-gradient-to-r from-gray-900 to-black rounded-2xl shadow-xl mb-8">
            <div class="px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        @auth
                        <h1 class="text-3xl font-bold text-white">Welcome back, {{ auth()->user()->name }}</h1>
                        @endauth
                        <p class="mt-2 text-white">Here's what's happening with your blood bank today.</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-right">
                            <p class="text-gray-300">Current Time</p>
                            <p class="text-2xl font-semibold text-white" id="current-time"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards with Hover Effects -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Donors -->
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
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <i class="ri-arrow-up-line"></i>
                                        <span class="sr-only">Increased by</span>
                                        {{ number_format(($totalDonors / max($totalDonors - 1, 1)) * 100 - 100, 1) }}%
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eligible Donors -->
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
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        <i class="ri-arrow-up-line"></i>
                                        <span class="sr-only">Increased by</span>
                                        {{ number_format(($eligibleDonors / max($totalDonors, 1)) * 100, 1) }}%
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blood Requests -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3">
                            <i class="ri-heart-pulse-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $pendingRequests }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Blood Units -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                            <i class="ri-drop-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Available Units</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $availableUnits }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blood Group Distribution with Chart -->
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
                                    {{ number_format(($count / max($totalDonors, 1)) * 100, 1) }}%
                                </div>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / max($totalDonors, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Donors -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Donors</h3>
                        <a href="{{ route('donors.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View all</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentDonors as $donor)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-white font-medium">{{ substr($donor->first_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $donor->first_name }} {{ $donor->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $donor->blood_group }} · 
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $donor->status === 'Legible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $donor->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $donor->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No recent donors
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Blood Requests -->
            <div class="bg-white rounded-xl shadow-sm">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Blood Requests</h3>
                        <a href="{{ route('blood-requests.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">View all</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentRequests as $request)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $request->hospital->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $request->blood_group }} · 
                                        <span class="font-medium">{{ $request->units }} units</span>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No recent blood requests
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- New Transfusions Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Transfusions</h2>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentTransfusions as $transfusion)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transfusion->patient->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transfusion->bloodBag->blood_group ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transfusion->transfusion_date ? $transfusion->transfusion_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transfusion->status ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
        document.getElementById('current-time').textContent = timeString;
    }
    
    updateTime();
    setInterval(updateTime, 1000);
</script>
@endpush
@endsection
