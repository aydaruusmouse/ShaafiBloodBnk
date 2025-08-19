@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Cross-Match Test</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('cross-matches.update', $crossMatch) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Blood Request Details</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>Patient:</strong> {{ $crossMatch->bloodRequest->patient->name }}</p>
                                    <p><strong>Blood Group:</strong> {{ $crossMatch->bloodRequest->blood_group }}</p>
                                    <p><strong>Component Type:</strong> {{ ucfirst(str_replace('_', ' ', $crossMatch->bloodRequest->component_type)) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="blood_bag_id" class="form-label">Select Blood Bag</label>
                            <select name="blood_bag_id" id="blood_bag_id" class="form-select @error('blood_bag_id') is-invalid @enderror" required>
                                <option value="">Select a blood bag</option>
                                @foreach($availableBags as $bag)
                                    <option value="{{ $bag->id }}" {{ old('blood_bag_id', $crossMatch->blood_bag_id) == $bag->id ? 'selected' : '' }}>
                                        {{ $bag->serial_number }} ({{ $bag->blood_group }} - {{ ucfirst(str_replace('_', ' ', $bag->component_type)) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_bag_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Compatibility</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_compatible" id="compatible" value="1" {{ old('is_compatible', $crossMatch->is_compatible) == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="compatible">
                                    Compatible
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_compatible" id="incompatible" value="0" {{ old('is_compatible', $crossMatch->is_compatible) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="incompatible">
                                    Incompatible
                                </label>
                            </div>
                            @error('is_compatible')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $crossMatch->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cross-matches.show', $crossMatch) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Cross-Match Test</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 