@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
  <div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-lg font-medium leading-6 text-gray-900">New Role</h1>

    <form action="{{ route('roles.store') }}" method="POST" class="space-y-5">
      @csrf

      <!-- Role Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
        <input
          type="text"
          name="name"
          id="name"
          value="{{ old('name') }}"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          placeholder="e.g. doctor"
        >
        @error('name')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          name="description"
          id="description"
          rows="3"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          placeholder="Optional detail about this role"
        >{{ old('description') }}</textarea>
        @error('description')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="pt-4">
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
        
          Create Role
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
