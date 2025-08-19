@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Role Details
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                View role information and associated users
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('roles.edit', $role) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="ri-edit-line mr-2"></i>
                Edit Role
            </a>
            <a href="{{ route('roles.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="ri-arrow-left-line mr-2"></i>
                Back to Roles
            </a>
        </div>
    </div>

    <!-- Role Information Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Role Information</h2>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-4">
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Role Name</dt>
                    <dd class="text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $role->name === 'admin' ? 'purple' : 'blue' }}-100 text-{{ $role->name === 'admin' ? 'purple' : 'blue' }}-800">
                            {{ ucfirst($role->name) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Description</dt>
                    <dd class="text-sm text-gray-900">{{ $role->description ?? 'No description provided' }}</dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Created At</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $role->created_at ? $role->created_at->format('M d, Y H:i') : 'N/A' }}
                    </dd>
                </div>
                <div class="flex items-center">
                    <dt class="text-sm font-medium text-gray-500 w-1/3">Last Updated</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $role->updated_at ? $role->updated_at->format('M d, Y H:i') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Users with this Role -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Users with this Role</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($role->users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="ri-user-line text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ri-user-line text-4xl text-gray-400 mb-2"></i>
                                <h3 class="text-sm font-medium text-gray-900">No users found</h3>
                                <p class="mt-1 text-sm text-gray-500">No users have been assigned to this role yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 