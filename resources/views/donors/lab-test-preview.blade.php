{{-- resources/views/donors/lab-test-preview.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto mt-10 px-6 py-8 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-bold mb-6">Confirm Lab Test Results</h1>

  <dl class="space-y-4">
    @foreach($results as $key => $value)
      <div class="p-4 bg-gray-50 rounded">
        <dt class="font-semibold">{{ $testLabels[$key] }}</dt>
        <dd class="mt-1">{{ ucfirst($value) }}</dd>
      </div>
    @endforeach
  </dl>

  <div class="mt-8 flex space-x-4">
    <a href="{{ route('donors.lab-test.results', $donor) }}"
       class="px-4 py-2 border rounded text-gray-700">◀ Edit</a>
    <form action="{{ route('donors.lab-test.store', $donor) }}" method="POST">
      @csrf
      <button type="submit"
              class="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Save and Continue
      </button>
    </form>
  </div>
</div>
@endsection
