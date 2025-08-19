@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Enter Blood Test Results</h1>
        <a href="{{ route('donors.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
            <i class="ri-arrow-left-line mr-1"></i> Back
        </a>
    </div>
    <div class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-lg bg-gray-50 ring-1 ring-gray-200">
                <div class="text-gray-500">Donor ID</div>
                <div class="mt-1 font-medium text-gray-900 tracking-wider">{{ str_pad($donor->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="p-4 rounded-lg bg-gray-50 ring-1 ring-gray-200">
                <div class="text-gray-500">Donor Name</div>
                <div class="mt-1 font-medium text-gray-900">{{ $donor->first_name }} {{ $donor->last_name }}</div>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('donors.lab-test.select.post', $donor) }}">
        @csrf
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-3">Infectious Disease Testing</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($tests as $key => $label)
                    <label for="test_{{ $key }}" class="flex items-center p-3 rounded-lg bg-gray-50 hover:bg-gray-100 ring-1 ring-gray-200 cursor-pointer">
                        <input id="test_{{ $key }}" name="tests[]" type="checkbox" value="{{ $key }}" class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded"/>
                        <span class="ml-3 text-sm font-medium text-gray-800">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('tests')
                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="border-t border-gray-200 pt-4 flex justify-end">
            <button type="submit" 
                    class="w-full sm:w-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="ri-save-3-line mr-1"></i>
                Save
            </button>
        </div>
    </form>
</div>
@endsection 