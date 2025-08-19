@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit User</h1>
      <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800">
        <i class="ri-arrow-left-line text-lg"></i> Back to Users
      </a>
    </div>

    @if(session('error'))
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
      </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
      @csrf
      @method('PUT')

      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          type="text"
          name="name"
          id="name"
          value="{{ old('name', $user->name) }}"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
        @error('name')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email', $user->email) }}"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
        @error('email')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Role -->
      <div>
        <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
        <select
          name="role_id"
          id="role_id"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
          @foreach($roles as $id => $name)
            <option value="{{ $id }}" {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
              {{ ucfirst($name) }}
            </option>
          @endforeach
        </select>
        @error('role_id')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
        <input
          type="password"
          name="password"
          id="password"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
        @error('password')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
        <input
          type="password"
          name="password_confirmation"
          id="password_confirmation"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        >
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
        >
          Update User
        </button>
      </div>
    </form>
  </div>
</div>
@endsection 