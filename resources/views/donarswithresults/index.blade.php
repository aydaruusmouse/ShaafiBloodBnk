@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Donor Lab Results</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Donor ID</th>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>Test Date</th>
                            <th>HIV</th>
                            <th>Hepatitis B</th>
                            <th>Hepatitis C</th>
                            <th>Syphilis</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donors as $donor)
                            @php
                                $test = $donor->labTests->first();
                                $isEligible = $test && 
                                    $test->hiv === 'negative' && 
                                    $test->hepatitis_b === 'negative' && 
                                    $test->hepatitis_c === 'negative' && 
                                    $test->syphilis === 'negative';
                            @endphp
                            <tr>
                                <td>#{{ $donor->id }}</td>
                                <td>{{ $donor->first_name }} {{ $donor->last_name }}</td>
                                <td>{{ $donor->blood_group ?? 'N/A' }}</td>
                                <td>{{ $test ? $test->test_date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @if($test)
                                        <span class="badge bg-{{ $test->hiv === 'negative' ? 'success' : 'danger' }}">
                                            {{ ucfirst($test->hiv) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($test)
                                        <span class="badge bg-{{ $test->hepatitis_b === 'negative' ? 'success' : 'danger' }}">
                                            {{ ucfirst($test->hepatitis_b) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($test)
                                        <span class="badge bg-{{ $test->hepatitis_c === 'negative' ? 'success' : 'danger' }}">
                                            {{ ucfirst($test->hepatitis_c) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($test)
                                        <span class="badge bg-{{ $test->syphilis === 'negative' ? 'success' : 'danger' }}">
                                            {{ ucfirst($test->syphilis) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($test)
                                        <span class="badge bg-{{ $isEligible ? 'success' : 'danger' }}">
                                            {{ $isEligible ? 'Eligible' : 'Not Eligible' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Tests</span>
                                    @endif
                                </td>
                                <td>
                                    @if($test)
                                        <a href="{{ route('donors.lab-results.show', ['donor' => $donor->id, 'test' => $test->id]) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">No Tests</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No donors with lab results found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $donors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection