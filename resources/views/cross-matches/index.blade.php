@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cross-Match Tests</h5>
                    <div class="d-flex">
                        <form action="{{ route('cross-matches.search') }}" method="GET" class="me-2">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="Search by patient or blood bag..." value="{{ request('query') }}">
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
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Blood Bag</th>
                                    <th>Compatibility</th>
                                    <th>Performed By</th>
                                    <th>Performed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($crossMatches as $crossMatch)
                                    <tr>
                                        <td>{{ $crossMatch->id }}</td>
                                        <td>
                                            <a href="{{ route('patients.show', $crossMatch->patient) }}">
                                                {{ $crossMatch->patient->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('blood-bags.show', $crossMatch->bloodBag) }}">
                                                {{ $crossMatch->bloodBag->serial_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $crossMatch->is_compatible ? 'success' : 'danger' }}">
                                                {{ $crossMatch->is_compatible ? 'Compatible' : 'Incompatible' }}
                                            </span>
                                        </td>
                                        <td>{{ $crossMatch->performed_by }}</td>
                                        <td>{{ $crossMatch->performed_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('cross-matches.show', $crossMatch) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('cross-matches.edit', $crossMatch) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('cross-matches.destroy', $crossMatch) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this cross-match test?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No cross-match tests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $crossMatches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 