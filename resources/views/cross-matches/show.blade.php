@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cross-Match Test Details</h5>
                    <div class="btn-group">
                        <a href="{{ route('cross-matches.edit', $crossMatch) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('cross-matches.destroy', $crossMatch) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this cross-match test?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Patient Information</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>Name:</strong> 
                                        <a href="{{ route('patients.show', $crossMatch->patient) }}">
                                            {{ $crossMatch->patient->name }}
                                        </a>
                                    </p>
                                    <p><strong>Blood Group:</strong> {{ $crossMatch->bloodRequest->blood_group }}</p>
                                    <p><strong>Component Type:</strong> {{ ucfirst(str_replace('_', ' ', $crossMatch->bloodRequest->component_type)) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6>Blood Bag Information</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>Serial Number:</strong> 
                                        <a href="{{ route('blood-bags.show', $crossMatch->bloodBag) }}">
                                            {{ $crossMatch->bloodBag->serial_number }}
                                        </a>
                                    </p>
                                    <p><strong>Blood Group:</strong> {{ $crossMatch->bloodBag->blood_group }}</p>
                                    <p><strong>Component Type:</strong> {{ ucfirst(str_replace('_', ' ', $crossMatch->bloodBag->component_type)) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Test Results</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p>
                                        <strong>Compatibility:</strong>
                                        <span class="badge bg-{{ $crossMatch->is_compatible ? 'success' : 'danger' }}">
                                            {{ $crossMatch->is_compatible ? 'Compatible' : 'Incompatible' }}
                                        </span>
                                    </p>
                                    <p><strong>Performed By:</strong> {{ $crossMatch->performed_by }}</p>
                                    <p><strong>Performed At:</strong> {{ $crossMatch->performed_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6>Additional Information</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>Notes:</strong></p>
                                    <p>{{ $crossMatch->notes ?? 'No notes available' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cross-matches.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('blood-requests.show', $crossMatch->bloodRequest) }}" class="btn btn-info">
                            View Blood Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 