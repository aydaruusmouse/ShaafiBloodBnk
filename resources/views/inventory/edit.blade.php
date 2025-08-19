<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Blood Unit') }}
            </h2>
            <a href="{{ route('inventory.show', $inventory) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('inventory.update', $inventory) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Blood Group -->
                            <div>
                                <label for="blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                                <select name="blood_group" id="blood_group" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($bloodGroups as $group)
                                        <option value="{{ $group }}" {{ old('blood_group', $inventory->blood_group) == $group ? 'selected' : '' }}>
                                            {{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blood_group')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Component -->
                            <div>
                                <label for="component_id" class="block text-sm font-medium text-gray-700">Component</label>
                                <select name="component_id" id="component_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($components as $component)
                                        <option value="{{ $component->id }}" {{ old('component_id', $inventory->component_id) == $component->id ? 'selected' : '' }}>
                                            {{ $component->name }} ({{ $component->shelf_life_days }} days shelf life)
                                        </option>
                                    @endforeach
                                </select>
                                @error('component_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Storage Location -->
                            <div>
                                <label for="storage_location_id" class="block text-sm font-medium text-gray-700">Storage Location</label>
                                <select name="storage_location_id" id="storage_location_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($storageLocations as $location)
                                        <option value="{{ $location->id }}" {{ old('storage_location_id', $inventory->storage_location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }} ({{ $location->temperature }}°C)
                                        </option>
                                    @endforeach
                                </select>
                                @error('storage_location_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="available" {{ old('status', $inventory->status) == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="reserved" {{ old('status', $inventory->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                    <option value="used" {{ old('status', $inventory->status) == 'used' ? 'selected' : '' }}>Used</option>
                                    <option value="expired" {{ old('status', $inventory->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Test Status -->
                            <div>
                                <label for="test_status" class="block text-sm font-medium text-gray-700">Test Status</label>
                                <select name="test_status" id="test_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" {{ old('test_status', $inventory->test_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="passed" {{ old('test_status', $inventory->test_status) == 'passed' ? 'selected' : '' }}>Passed</option>
                                    <option value="failed" {{ old('test_status', $inventory->test_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('test_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Test Date -->
                            <div>
                                <label for="test_date" class="block text-sm font-medium text-gray-700">Test Date</label>
                                <input type="date" name="test_date" id="test_date"
                                    value="{{ old('test_date', $inventory->test_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('test_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tested By -->
                            <div>
                                <label for="tested_by" class="block text-sm font-medium text-gray-700">Tested By</label>
                                <input type="text" name="tested_by" id="tested_by"
                                    value="{{ old('tested_by', $inventory->tested_by) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('tested_by')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $inventory->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('inventory.show', $inventory) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Blood Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 