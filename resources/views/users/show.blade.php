@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                User Details
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                View user information and role
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="ri-edit-line mr-2"></i>
                Edit User
            </a>
            <a href="{{ route('users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="ri-arrow-left-line mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <!-- User Information Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">User Information</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-4">
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Name</dt>
                    <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Role</dt>
                    <dd class="text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->role->name === 'admin' ? 'purple' : 'blue' }}-100 text-{{ $user->role->name === 'admin' ? 'purple' : 'blue' }}-800">
                            {{ ucfirst($user->role->name) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Created At</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Last Updated</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $user->updated_at ? $user->updated_at->format('M d, Y H:i') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection 