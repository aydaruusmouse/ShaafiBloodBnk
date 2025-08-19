@extends('layouts.app')

@section('title', 'Manage Hospitals')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manage Hospitals</h1>
                <p class="mt-2 text-sm text-gray-600">Create, edit, and manage hospital tenants</p>
            </div>
            <a href="{{ route('super-admin.hospitals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="ri-add-line mr-2"></i>
                Add Hospital
            </a>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('super-admin.hospitals') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Filter by City</label>
                        <select id="city" name="city" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Cities</option>
                            @foreach(\App\Models\Hospital::withoutGlobalScopes()->distinct()->pluck('city')->filter() as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="ri-search-line mr-2"></i>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="ri-check-line text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hospital Credentials Display -->
        @if(session('hospital_credentials'))
            @php $creds = session('hospital_credentials') @endphp
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="ri-information-line text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Hospital Created Successfully!</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p><strong>Hospital:</strong> {{ $creds['hospital'] }}</p>
                            <p><strong>Admin Email:</strong> {{ $creds['admin_email'] }}</p>
                            <p><strong>Admin Password:</strong> {{ $creds['admin_password'] }}</p>
                            <p class="mt-2 text-xs">Please save these credentials securely. The admin should change the password upon first login.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('reset_credentials'))
            @php $creds = session('reset_credentials') @endphp
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="ri-key-line text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Admin Password Reset!</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p><strong>Hospital:</strong> {{ $creds['hospital'] }}</p>
                            <p><strong>Admin Email:</strong> {{ $creds['admin_email'] }}</p>
                            <p><strong>New Password:</strong> {{ $creds['admin_password'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hospitals Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            @if($hospitals->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($hospitals as $hospital)
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="ri-hospital-line text-2xl text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $hospital->name }}</h3>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hospital->status_badge_class }}">
                                            {{ ucfirst($hospital->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <span class="mr-4"><i class="ri-map-pin-line mr-1"></i>{{ $hospital->city }}</span>
                                        <span class="mr-4"><i class="ri-phone-line mr-1"></i>{{ $hospital->phone }}</span>
                                        <span class="mr-4"><i class="ri-mail-line mr-1"></i>{{ $hospital->email }}</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-400">
                                        <span class="mr-4"><i class="ri-building-line mr-1"></i>{{ $hospital->departments_count }} departments</span>
                                        <span><i class="ri-user-line mr-1"></i>{{ $hospital->users_count }} users</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('super-admin.hospitals.edit', $hospital) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="ri-edit-line mr-2"></i>
                                    Edit
                                </a>
                                <form action="{{ route('super-admin.hospitals.reset-admin', $hospital) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm leading-4 font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" onclick="return confirm('Reset admin password for {{ $hospital->name }}?')">
                                        <i class="ri-key-line mr-2"></i>
                                        Reset Admin
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                
                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $hospitals->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="ri-hospital-line text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hospitals found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first hospital.</p>
                    <a href="{{ route('super-admin.hospitals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="ri-add-line mr-2"></i>
                        Add Hospital
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 