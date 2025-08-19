@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Staff & Users</h2>
        <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md px-4 py-2">
            <i class="ri-add-line text-lg mr-2"></i>
            New User
        </a>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" x-transition class="mb-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg overflow-hidden w-full">
        <div class="overflow-x-auto w-full border-gray-200">
            <table class="w-full table-auto">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="ri-user-line text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $user->role->name === 'admin' ? 'purple' : 'blue' }}-100 text-{{ $user->role->name === 'admin' ? 'purple' : 'blue' }}-800">
                                {{ ucfirst($user->role->name) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('users.edit', $user) }}" 
                                   class="text-indigo-600 hover:text-indigo-800" 
                                   title="Edit">
                                    <i class="ri-edit-line text-lg"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            title="Delete">
                                        <i class="ri-delete-bin-6-line text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                                <div class="mt-6">
                                    <a href="{{ route('users.create') }}" class="w-full md:w-auto px-8 py-3 bg-green-500 text-white text-base font-semibold rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                                        New User
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="mt-4 flex items-center justify-center">
        {{ $users->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pagination li {
        margin: 0 0.25rem;
    }
    .pagination .page-link {
        @apply px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100;
    }
    .pagination .active .page-link {
        @apply bg-blue-600 text-white;
    }
    .pagination .disabled .page-link {
        @apply text-gray-400 cursor-not-allowed;
    }
</style>
@endpush
@endsection
