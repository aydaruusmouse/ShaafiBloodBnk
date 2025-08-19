@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">System Roles</h2>
        <a href="{{ route('roles.create') }}" class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-md px-4 py-2">
            
            New Role
        </a>
    </div>

    @if(session('success'))
    <div
      x-data="{ show: true }"
      x-init="setTimeout(() => show = false, 2000)"
      x-show="show"
      x-transition
      class="mb-6"
    >
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
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Users</th>
                        <th class="px-6 py-4 text-left text-xs font-normal text-gray-900 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $role->name === 'admin' ? 'purple' : 'blue' }}-100 text-{{ $role->name === 'admin' ? 'purple' : 'blue' }}-800">
                                {{ ucfirst($role->name) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $role->description ?? 'No description provided' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-block w-2 h-2 mr-2 rounded-full bg-green-500"></span>
                                {{ $role->users_count }} {{ Str::plural('User', $role->users_count) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('roles.show', $role) }}" 
                                   class="text-gray-600 hover:text-gray-800" 
                                   title="View">
                                    <i class="ri-eye-line text-lg"></i>
                                </a>
                                <a href="{{ route('roles.edit', $role) }}" 
                                   class="text-indigo-600 hover:text-indigo-800" 
                                   title="Edit">
                                    <i class="ri-edit-line text-lg"></i>
                                </a>
                                <form action="{{ route('roles.destroy', $role) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this role?')" 
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
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new role.</p>
                                <div class="mt-6 bg-green-500">
                                    <a href="{{ route('roles.create') }}" class="w-full md:w-auto px-8 py-3 text-white text-base font-semibold rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                                        New Role
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

    <!-- Pagination -->
    @if($roles->hasPages())
    <div class="mt-4 flex items-center justify-center">
        {{ $roles->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
    .pagination {
        @apply flex items-center justify-center space-x-2;
    }
    .pagination .page-link {
        @apply px-4 py-2 border rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50;
    }
    .pagination .page-item.active .page-link {
        @apply bg-blue-600 text-white border-blue-600;
    }
    .pagination .page-item.disabled .page-link {
        @apply text-gray-400 cursor-not-allowed hover:bg-transparent;
    }
</style>
@endpush

@endsection