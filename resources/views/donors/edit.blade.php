@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Update Donor</h1>

        <form action="{{ route('donors.update', $donor) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <fieldset class="border border-gray-200 rounded-lg p-6">
                <legend class="text-lg font-semibold text-gray-700">Personal Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $donor->first_name) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('first_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $donor->last_name) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('last_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" 
                            value="{{ old('date_of_birth', $donor->date_of_birth ? $donor->date_of_birth->format('Y-m-d') : '') }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('date_of_birth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                        <select name="sex" id="sex" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                            <option value="">Select sex</option>
                            <option value="male" {{ old('sex', $donor->sex)=='male'? 'selected':'' }}>Male</option>
                            <option value="female" {{ old('sex', $donor->sex)=='female'? 'selected':'' }}>Female</option>
                        </select>
                        @error('sex')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                        <input type="number" name="age" id="age" value="{{ old('age', $donor->age) }}" min="18" max="65" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('age')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>

            <!-- Contact & Location -->
            <fieldset class="border border-gray-200 rounded-lg p-6">
                <legend class="text-lg font-semibold text-gray-700">Contact & Location</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="md:col-span-2">
                        <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                        <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $donor->occupation) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('occupation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="village" class="block text-sm font-medium text-gray-700">Village</label>
                        <input type="text" name="village" id="village" value="{{ old('village', $donor->village) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('village')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="tell" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="tell" id="tell" value="{{ old('tell', $donor->tell) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('tell')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>

            <!-- Health Information -->
            <fieldset class="border border-gray-200 rounded-lg p-6">
                <legend class="text-lg font-semibold text-gray-700">Health Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" step="0.1" value="{{ old('weight', $donor->weight) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('weight')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="bp" class="block text-sm font-medium text-gray-700">BP (Sys/Dia)</label>
                        <input type="text" name="bp" id="bp" placeholder="120/80" value="{{ old('bp', $donor->bp) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('bp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hemoglobin" class="block text-sm font-medium text-gray-700">Hemoglobin (g/dL)</label>
                        <input type="text" name="hemoglobin" id="hemoglobin" value="{{ old('hemoglobin', $donor->hemoglobin) }}" 
                            class="mt-1 block w-full px-4 py-2 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent" required>
                        @error('hemoglobin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>

            <!-- Submit -->
            <div class="text-right">
                <button type="submit" 
                      class="w-full md:w-auto px-8 py-3 bg-green-500 text-white text-base font-semibold rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                    Update Donor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
