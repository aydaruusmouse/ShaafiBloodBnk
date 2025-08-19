@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Reports</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Blood Requests Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Blood Requests Report</h2>
                    <p class="text-gray-600 mb-4">Generate detailed reports of blood requests with various filters.</p>
                    <a href="{{ route('reports.blood-requests') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Report
                    </a>
                </div>
            </div>

            <!-- Blood Type Distribution -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Blood Type Distribution</h2>
                    <p class="text-gray-600 mb-4">View the distribution of blood types across all requests.</p>
                    <a href="{{ route('reports.blood-type-distribution') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Report
                    </a>
                </div>
            </div>

            <!-- Hospital Statistics -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Hospital Statistics</h2>
                    <p class="text-gray-600 mb-4">View statistics and metrics for each hospital.</p>
                    <a href="{{ route('reports.hospital-statistics') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Report
                    </a>
                </div>
            </div>

            <!-- Department Statistics -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Department Statistics</h2>
                    <p class="text-gray-600 mb-4">View statistics and metrics for each department.</p>
                    <a href="{{ route('reports.department-statistics') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 