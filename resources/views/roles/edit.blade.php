@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Role</h1>
      <a href="{{ route('roles.index') }}" class="text-gray-600 hover:text-gray-800">
        <i class="ri-arrow-left-line text-lg"></i> Back to Roles
      </a>
    </div>

    @if(session('error'))
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
      </div>
    @endif

    <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-5">
      @csrf
      @method('PUT')

      <!-- Role Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
        <input
          type="text"
          name="name"
          id="name"
          value="{{ old('name', $role->name) }}"
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
        >{{ old('description', $role->description) }}</textarea>
        @error('description')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="pt-4">
        <button
          type="submit"
          class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md px-4 py-2">
          Update Role
        </button>
      </div>
    </form>
  </div>
</div>
@endsection 