@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="sm:flex sm:items-center mb-8">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold text-gray-900">Blood Transfusion Form</h1>
                <p class="mt-2 text-sm text-gray-700">Enter patient information and transfusion details.</p>
            </div>
        </div>

        @php
            $bagGroups = $bloodBags->pluck('blood_group')->filter()->values();
            $uniqueGroups = $bagGroups->unique();
            $prefilledGroup = $uniqueGroups->first();
            $hasMixedGroups = $uniqueGroups->count() > 1;
        @endphp

        <form action="{{ route('blood-bags.complete-transfusion') }}" method="POST" class="space-y-8">
            @csrf
            <input type="hidden" name="blood_bag_ids" value="{{ $bloodBagIds }}">
            <input type="hidden" name="patient_blood_group" value="{{ $prefilledGroup }}">

            {{-- Patient Information --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Patient Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Enter the patient's details.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="patient_name" class="block text-sm font-medium text-gray-700">Patient Name</label>
                            <div class="mt-1">
                                <input type="text" name="patient_name" id="patient_name" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Blood Group</label>
                            <div class="mt-1">
                                <input type="text" value="{{ $prefilledGroup }}" disabled
                                       class="shadow-sm bg-gray-50 text-gray-700 focus:ring-0 focus:border-gray-300 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @if($hasMixedGroups)
                                <p class="mt-2 text-sm text-red-600">Selected bags have mixed blood groups ({{ $uniqueGroups->implode(', ') }}). Please go back and select compatible bags only.</p>
                            @endif
                        </div>

                        <div class="sm:col-span-3">
                            <label for="patient_age" class="block text-sm font-medium text-gray-700">Age</label>
                            <div class="mt-1">
                                <input type="number" name="patient_age" id="patient_age" min="0" max="120" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="patient_gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <div class="mt-1">
                                <select name="patient_gender" id="patient_gender" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="patient_medical_history" class="block text-sm font-medium text-gray-700">Medical History</label>
                            <div class="mt-1">
                                <textarea name="patient_medical_history" id="patient_medical_history" rows="3"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Crossmatching --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Crossmatching</label>
                        <div class="mt-2 flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="crossmatch" value="compatible" class="h-4 w-4 text-green-600 border-gray-300" required>
                                <span class="ml-2 text-sm text-gray-800">Compatible</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="crossmatch" value="incompatible" class="h-4 w-4 text-red-600 border-gray-300" required>
                                <span class="ml-2 text-sm text-gray-800">Incompatible</span>
                            </label>
                        </div>
                        <p id="crossmatchNotice" class="hidden mt-2 text-sm text-red-600">Transfusion cannot proceed when crossmatch is incompatible.</p>
                    </div>
                </div>
            </div>

            {{-- Transfusion Details --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Transfusion Details</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Enter the transfusion information.</p>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="transfusion_date" class="block text-sm font-medium text-gray-700">Transfusion Date</label>
                            <div class="mt-1">
                                <input type="datetime-local" name="transfusion_date" id="transfusion_date" required
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="transfusion_reason" class="block text-sm font-medium text-gray-700">Reason for Transfusion</label>
                            <div class="mt-1">
                                <input type="text" name="transfusion_reason" id="transfusion_reason" required
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="transfusion_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1">
                                <textarea name="transfusion_notes" id="transfusion_notes" rows="3"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('blood-bags.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" id="completeTransfusionBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Complete Transfusion
                </button>
            </div>
        </form>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function(){
            const radios = document.querySelectorAll('input[name="crossmatch"]');
            const btn = document.getElementById('completeTransfusionBtn');
            const notice = document.getElementById('crossmatchNotice');
            function update(){
                const sel = document.querySelector('input[name="crossmatch"]:checked');
                if (!sel) { btn.disabled = true; btn.classList.add('opacity-50','cursor-not-allowed'); notice.classList.add('hidden'); return; }
                if (sel.value === 'incompatible') { btn.disabled = true; btn.classList.add('opacity-50','cursor-not-allowed'); notice.classList.remove('hidden'); }
                else { btn.disabled = false; btn.classList.remove('opacity-50','cursor-not-allowed'); notice.classList.add('hidden'); }
            }
            radios.forEach(r => r.addEventListener('change', update));
            update();
        });
        </script>
        @endpush
    </div>
</div>
@endsection 