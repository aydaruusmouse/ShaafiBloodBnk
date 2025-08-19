@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Create SMS Campaign</h1>
            <a href="{{ route('sms-campaigns.index') }}" class="text-blue-600 hover:text-blue-900">
                Back to Campaigns
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('sms-campaigns.store') }}" method="POST" class="p-6" id="campaignForm">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Campaign Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Campaign Type</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="scheduled" {{ old('type') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="auto" {{ old('type') == 'auto' ? 'selected' : '' }}>Auto-trigger</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Blood Type -->
                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700">Blood Type (Optional)</label>
                        <select name="blood_type" id="blood_type"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Blood Types</option>
                            @foreach($bloodTypes as $type)
                                <option value="{{ $type }}" {{ old('blood_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('blood_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview Recipients Button -->
                    <div>
                        <button type="button" id="previewRecipients" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="ri-eye-line mr-2"></i>
                            Preview Recipients
                        </button>
                        <span id="recipientCount" class="ml-3 text-sm text-gray-500"></span>
                    </div>

                    <!-- Scheduled At (shown only for scheduled campaigns) -->
                    <div id="scheduled_at_container" class="hidden">
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('scheduled_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Template -->
                    <div>
                        <label for="message_template" class="block text-sm font-medium text-gray-700">Message Template</label>
                        <div class="mt-1">
                            <textarea name="message_template" id="message_template" rows="4" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('message_template', 'Dear {name}, This is an urgent appeal for {blood_type} blood donation. Your help is needed. Please visit our blood bank as soon as possible. Thank you!') }}</textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Available placeholders: {name}, {blood_type}
                        </p>
                        @error('message_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>

        <!-- Test SMS Section -->
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Test SMS API</h3>
                
                <!-- Test Connection -->
                <div class="mb-6">
                    <button type="button" id="testConnection" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="ri-wifi-line mr-2"></i>
                        Test API Connection
                    </button>
                    <span id="connectionStatus" class="ml-3 text-sm"></span>
                </div>

                <!-- Send Test SMS -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Send Test SMS</h4>
                    <form id="testSmsForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="test_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="text" id="test_phone" name="phone_number" placeholder="+252634160295" required
                                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label for="test_message" class="block text-sm font-medium text-gray-700">Message</label>
                                <input type="text" id="test_message" name="message" placeholder="Test message from Blood Bank" required
                                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="ri-send-plane-line mr-2"></i>
                                Send Test SMS
                            </button>
                            <span id="testSmsStatus" class="ml-3 text-sm"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            </form>
        </div>
    </div>
</div>

<!-- Recipients Preview Modal -->
<div id="recipientsModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Recipients Preview
                        </h3>
                        <div class="mt-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Donation</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="recipientsList">
                                        <!-- Recipients will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide scheduled_at field based on campaign type
    document.getElementById('type').addEventListener('change', function() {
        const scheduledContainer = document.getElementById('scheduled_at_container');
        if (this.value === 'scheduled') {
            scheduledContainer.classList.remove('hidden');
        } else {
            scheduledContainer.classList.add('hidden');
        }
    });

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        if (typeSelect.value === 'scheduled') {
            document.getElementById('scheduled_at_container').classList.remove('hidden');
        }
    });

    // Preview Recipients functionality
    const modal = document.getElementById('recipientsModal');
    const previewBtn = document.getElementById('previewRecipients');
    const closeBtn = document.getElementById('closeModal');
    const recipientsList = document.getElementById('recipientsList');
    const recipientCount = document.getElementById('recipientCount');

    previewBtn.addEventListener('click', async function() {
        const bloodType = document.getElementById('blood_type').value;
        const messageTemplate = document.getElementById('message_template').value;

        try {
            const response = await fetch(`/api/preview-recipients?blood_type=${bloodType}`);
            const data = await response.json();

            // Update recipient count
            recipientCount.textContent = `${data.recipients.length} recipients found`;

            // Clear and populate recipients list
            recipientsList.innerHTML = '';
            data.recipients.forEach(recipient => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${recipient.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${recipient.blood_group}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${recipient.phone}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${recipient.last_donation || 'Never'}</td>
                `;
                recipientsList.appendChild(row);
            });

            modal.classList.remove('hidden');
        } catch (error) {
            console.error('Error fetching recipients:', error);
            alert('Error loading recipients preview');
        }
    });

    closeBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Test SMS API Connection
    document.getElementById('testConnection').addEventListener('click', async function() {
        const statusSpan = document.getElementById('connectionStatus');
        const button = this;
        
        button.disabled = true;
        button.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Testing...';
        statusSpan.textContent = '';
        
        try {
            const response = await fetch('{{ route("sms-campaigns.test-connection") }}');
            const data = await response.json();
            
            if (data.success) {
                statusSpan.textContent = '✅ Connection successful';
                statusSpan.className = 'ml-3 text-sm text-green-600';
            } else {
                statusSpan.textContent = '❌ Connection failed: ' + data.message;
                statusSpan.className = 'ml-3 text-sm text-red-600';
            }
        } catch (error) {
            statusSpan.textContent = '❌ Connection failed: ' + error.message;
            statusSpan.className = 'ml-3 text-sm text-red-600';
        } finally {
            button.disabled = false;
            button.innerHTML = '<i class="ri-wifi-line mr-2"></i>Test API Connection';
        }
    });

    // Send Test SMS
    document.getElementById('testSmsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const statusSpan = document.getElementById('testSmsStatus');
        const submitButton = this.querySelector('button[type="submit"]');
        const formData = new FormData(this);
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Sending...';
        statusSpan.textContent = '';
        
        try {
            const response = await fetch('{{ route("sms-campaigns.send-test") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                statusSpan.textContent = '✅ Test SMS sent successfully';
                statusSpan.className = 'ml-3 text-sm text-green-600';
                this.reset();
            } else {
                statusSpan.textContent = '❌ Failed to send: ' + data.message;
                statusSpan.className = 'ml-3 text-sm text-red-600';
            }
        } catch (error) {
            statusSpan.textContent = '❌ Error: ' + error.message;
            statusSpan.className = 'ml-3 text-sm text-red-600';
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="ri-send-plane-line mr-2"></i>Send Test SMS';
        }
    });
</script>
@endpush
@endsection 