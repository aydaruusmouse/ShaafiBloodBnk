@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Blood Type Distribution</h1>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Reports
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Distribution Chart</h2>
                <canvas id="bloodTypeChart"></canvas>
            </div>

            <!-- Statistics -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Statistics</h2>
                <div class="space-y-4">
                    @foreach($distribution as $item)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $item->blood_group }}</span>
                            <span class="text-sm text-gray-600">{{ $item->total }} requests</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($item->total / $distribution->sum('total')) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bloodTypeChart').getContext('2d');
    const data = @json($distribution);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.blood_group),
            datasets: [{
                data: data.map(item => item.total),
                backgroundColor: [
                    '#EF4444', // Red
                    '#F97316', // Orange
                    '#F59E0B', // Yellow
                    '#10B981', // Green
                    '#3B82F6', // Blue
                    '#6366F1', // Indigo
                    '#8B5CF6', // Purple
                    '#EC4899', // Pink
                ],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'Blood Type Distribution'
                }
            }
        }
    });
});
</script>
@endpush
@endsection 