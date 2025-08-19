@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Record Test Results</h3>
                                  
                                <a href="{{ route('donors.lab-test.select', $donor) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-white text-gray-700 border border-gray-300 hover:bg-gray-50">
                    <i class="ri-arrow-left-line mr-1"></i> Back
                </a>
            </div>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                Error!
                            </p>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <p class="text-sm text-gray-500">Donor</p>
                <p class="mt-1 text-lg font-medium text-gray-900">{{ $donor->first_name }} {{ $donor->last_name }}</p>
            </div>

            <form method="POST" action="{{ route('donors.lab-test.results.post', $donor) }}" class="space-y-6">
                @csrf
                
                @foreach($tests as $test)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ $testLabels[$test] }}</label>
                        <div class="mt-2 space-y-2">
                            @foreach(['positive' => 'Positive', 'negative' => 'Negative', 'inconclusive' => 'Inconclusive'] as $value => $label)
                            <div class="flex items-center">
                                <input id="{{ $test }}_{{ $value }}" name="results[{{ $test }}]" type="radio" value="{{ $value }}" 
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                    {{ old('results.' . $test) === $value ? 'checked' : '' }} required>
                                <label for="{{ $test }}_{{ $value }}" class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('results.' . $test)
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endforeach

                <div class="pt-5">
                <div class="border-t border-gray-200 pt-4 pb-4 flex justify-end">
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="ri-save-3-line mr-1"></i>
                        Save
                    </button>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection