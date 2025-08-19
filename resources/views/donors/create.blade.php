@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto pb-6">
    <div class="bg-white rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Donor information</h2>

        <form action="{{ route('donors.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Name Fields -->
                <div class="flex flex-row gap-4">
                    <div class="flex flex-col gap-2 flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">First name</label>
                        <input type="text" name="first_name" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                               value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last name</label>
                        <input type="text" name="last_name" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('date_of_birth') }}" required>
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sex and Age -->
                <div class="flex flex-row gap-4">
                    <div class="flex flex-col gap-2 flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                        <select name="sex" 
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                                required>
                            <option value="">Select sex</option>
                            <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="age" 
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500"
                               value="{{ old('age') }}" required min="18" max="65">
                    </div>
                </div>

                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                    <input type="text" name="occupation" id="occupation" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('occupation') }}" required>
                    @error('occupation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Village -->
                <div>
                    <label for="village" class="block text-sm font-medium text-gray-700">Village</label>
                    <input type="text" name="village" id="village" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('village') }}" required>
                    @error('village')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tell -->
                <div>
                    <label for="tell" class="block text-sm font-medium text-gray-700">Tell</label>
                    <input type="tel" name="tell" id="tell" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('tell') }}" required>
                    @error('tell')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Health Information - Horizontal -->
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight') }}" min="0" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter weight in kg">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bp" class="block text-sm font-medium text-gray-700">BP (systolic/diastolic)</label>
                        <input type="text" name="bp" id="bp" value="{{ old('bp') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., 120/80">
                        @error('bp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="hemoglobin" class="block text-sm font-medium text-gray-700">Hemoglobin</label>
                        <input type="number" name="hemoglobin" id="hemoglobin" value="{{ old('hemoglobin') }}" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter hemoglobin level">
                        @error('hemoglobin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Blood Group -->
                <div class="mb-6">
                    <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-1">
                        Blood Group
                    </label>
                    <select name="blood_group" id="blood_group" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select blood group</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $group)
                            <option value="{{ $group }}" {{ old('blood_group') === $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                    @error('blood_group')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection