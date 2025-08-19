<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blood Unit Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('inventory.edit', $inventory) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit Unit
                </a>
                <a href="{{ route('inventory.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Inventory
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Batch Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->batch_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Blood Group</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->blood_group }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Component</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->component->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $inventory->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $inventory->status === 'reserved' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $inventory->status === 'used' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $inventory->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($inventory->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Storage Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Storage Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Storage Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->storageLocation->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Temperature</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->storageLocation->temperature }}°C</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Collection Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->collection_date->format('Y-m-d') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $inventory->expiry_date->format('Y-m-d') }}
                                        @if($inventory->isExpired())
                                            <span class="text-red-500">(Expired)</span>
                                        @elseif($inventory->expiry_date->diffInDays(now()) <= 7)
                                            <span class="text-yellow-500">(Expiring Soon)</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Testing Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Testing Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Test Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $inventory->test_status === 'passed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $inventory->test_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $inventory->test_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($inventory->test_status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($inventory->test_date)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Test Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $inventory->test_date->format('Y-m-d') }}</dd>
                                    </div>
                                @endif
                                @if($inventory->tested_by)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tested By</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $inventory->tested_by }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                @if($inventory->donor)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Donor</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a href="{{ route('donors.show', $inventory->donor) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $inventory->donor->name }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $inventory->barcode }}</dd>
                                </div>
                                @if($inventory->notes)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $inventory->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 