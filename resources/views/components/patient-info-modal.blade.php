<!-- Patient Information Modal -->
<div id="patientInfoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closePatientModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="ri-close-line text-2xl"></i>
                </button>
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Patient Information
                </h3>
            </div>

            <!-- Body -->
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <form id="patientInfoForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Patient Name -->
                        <div>
                            <label for="patient_name" class="block text-sm font-medium text-gray-700">Patient Name</label>
                            <input type="text" name="patient_name" id="patient_name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Medical Record Number -->
                        <div>
                            <label for="medical_record_number" class="block text-sm font-medium text-gray-700">Medical Record Number</label>
                            <input type="text" name="medical_record_number" id="medical_record_number" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="patient_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="patient_phone" id="patient_phone" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="patient_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="patient_address" id="patient_address" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Blood Group -->
                        <div>
                            <label for="patient_blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                            <select name="patient_blood_group" id="patient_blood_group" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>

                        <!-- Hospital -->
                        <div>
                            <label for="hospital" class="block text-sm font-medium text-gray-700">Hospital</label>
                            <input type="text" name="hospital" id="hospital" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                            <input type="text" name="department" id="department" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Medical History -->
                        <div class="md:col-span-2">
                            <label for="medical_history" class="block text-sm font-medium text-gray-700">Medical History</label>
                            <textarea name="medical_history" id="medical_history" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="savePatientInfo()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save Information
                </button>
                <button type="button" onclick="closePatientModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showPatientModal() {
        document.getElementById('patientInfoModal').classList.remove('hidden');
    }

    function closePatientModal() {
        document.getElementById('patientInfoModal').classList.add('hidden');
    }

    function savePatientInfo() {
        const form = document.getElementById('patientInfoForm');
        const formData = new FormData(form);
        
        // Add patient info to hidden fields in the main form
        for (let [key, value] of formData.entries()) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = `patient_${key}`;
            hiddenField.value = value;
            document.querySelector('form[action="{{ route('donors.store') }}"]').appendChild(hiddenField);
        }
        
        closePatientModal();
    }

    // Close modal when clicking outside
    document.getElementById('patientInfoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePatientModal();
        }
    });
</script>
@endpush 