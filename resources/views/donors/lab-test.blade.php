@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Record Lab Test Results</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('donors.store-lab-test', $donor) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Donor Information</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>Donor Number:</strong> {{ $donor->donor_number }}</p>
                                    <p><strong>Name:</strong> {{ $donor->name }}</p>
                                    <p><strong>Blood Group:</strong> {{ $donor->blood_group }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Test Results</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="hbv" id="hbv" value="1" {{ old('hbv') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hbv">
                                            HBV (Hepatitis B) Positive
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="hcv" id="hcv" value="1" {{ old('hcv') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hcv">
                                            HCV (Hepatitis C) Positive
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="syphilis" id="syphilis" value="1" {{ old('syphilis') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="syphilis">
                                            Syphilis Positive
                                        </label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="hiv" id="hiv" value="1" {{ old('hiv') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hiv">
                                            HIV Positive
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tested_by" class="form-label">Tested By</label>
                            <input type="text" name="tested_by" id="tested_by" class="form-control @error('tested_by') is-invalid @enderror" value="{{ old('tested_by') }}" required>
                            @error('tested_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="test_date" class="form-label">Test Date</label>
                            <input type="date" name="test_date" id="test_date" class="form-control @error('test_date') is-invalid @enderror" value="{{ old('test_date', date('Y-m-d')) }}" required>
                            @error('test_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                <button type="submit" 
                        class="w-full md:w-auto px-8 py-3 bg-green-500 text-white text-base font-semibold rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                    Save
                </button>
            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 