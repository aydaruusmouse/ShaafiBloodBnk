@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Record New Blood Transfusion</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('transfusions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transfusions.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="patient_id" class="form-label">Patient *</label>
                        <select name="patient_id" id="patient_id" class="form-select" required>
                            <option value="">Select Patient</option>
                            @foreach(\App\Models\Patient::all() as $patient)
                                <option value="{{ $patient->id }}">
                                    {{ $patient->name }} ({{ $patient->blood_group }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="blood_bag_id" class="form-label">Select Blood Bag *</label>
                        <select name="blood_bag_id" id="blood_bag_id" class="form-select" required>
                            <option value="">Select Blood Bag</option>
                            @foreach($compatibleBlood as $bloodBag)
                                <option value="{{ $bloodBag->id }}">
                                    {{ $bloodBag->serial_number }} ({{ strtoupper($bloodBag->component_type) }} - {{ $bloodBag->blood_group }})
                                    - Expires: {{ $bloodBag->expiry_date->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="transfusion_date" class="form-label">Transfusion Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="transfusion_date" 
                               name="transfusion_date" value="{{ old('transfusion_date', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reason" class="form-label">Reason for Transfusion *</label>
                        <input type="text" class="form-control" id="reason" name="reason" value="{{ old('reason') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Record Transfusion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection