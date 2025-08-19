@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lab Tests</h5>
                    <div class="d-flex">
                        <form action="{{ route('lab-tests.search') }}" method="GET" class="me-2">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="Search by donor name or ID..." value="{{ request('query') }}">
                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Test Date</th>
                                    <th>Donor</th>
                                    <th>Tested By</th>
                                    <th>HBV</th>
                                    <th>HCV</th>
                                    <th>Syphilis</th>
                                    <th>HIV</th>
                                    <th>Eligibility</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labTests as $test)
                                    <tr>
                                        <td>{{ $test->test_date->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('donors.show', $test->donor) }}">
                                                {{ $test->donor->name }} ({{ $test->donor->donor_number }})
                                            </a>
                                        </td>
                                        <td>{{ $test->tested_by }}</td>
                                        <td>
                                            <span class="badge bg-{{ $test->hbv ? 'danger' : 'success' }}">
                                                {{ $test->hbv ? 'Positive' : 'Negative' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $test->hcv ? 'danger' : 'success' }}">
                                                {{ $test->hcv ? 'Positive' : 'Negative' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $test->syphilis ? 'danger' : 'success' }}">
                                                {{ $test->syphilis ? 'Positive' : 'Negative' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $test->hiv ? 'danger' : 'success' }}">
                                                {{ $test->hiv ? 'Positive' : 'Negative' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $test->donor->is_eligible ? 'success' : 'danger' }}">
                                                {{ $test->donor->is_eligible ? 'Eligible' : 'Not Eligible' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('donors.show', $test->donor) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No lab tests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $labTests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 