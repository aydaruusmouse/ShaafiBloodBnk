@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Request {{ $shaafiRequest->reference_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $shaafiRequest->request_type_label }}</p>
            </div>
            <a href="{{ route('shaafi-requests.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">Back to list</a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded">{{ session('warning') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">User Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Mobile Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->mobile_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Blood Group</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->blood_group }}</dd>
                        </div>
                        @if($shaafiRequest->request_type === 'blood_request')
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Blood Quantity</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->blood_quantity }} bag(s)</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">City</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->city }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Hospital</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->hospital->name }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Additional Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shaafiRequest->additional_notes ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>

                @if($shaafiRequest->agent_notes || $shaafiRequest->scheduled_at)
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Follow-up</h2>
                    @if($shaafiRequest->scheduled_at)
                        <p class="text-sm text-gray-700 mb-2"><strong>Scheduled:</strong> {{ $shaafiRequest->scheduled_at->format('M d, Y H:i') }}</p>
                    @endif
                    @if($shaafiRequest->agent_notes)
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $shaafiRequest->agent_notes }}</p>
                    @endif
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Status</h2>
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $shaafiRequest->status_badge_class }}">
                        {{ ucfirst(str_replace('_', ' ', $shaafiRequest->status)) }}
                    </span>
                    <p class="mt-3 text-xs text-gray-500">Submitted {{ $shaafiRequest->created_at->format('M d, Y H:i') }}</p>
                    @if($shaafiRequest->reviewer)
                        <p class="mt-1 text-xs text-gray-500">Last reviewed by {{ $shaafiRequest->reviewer->name }}</p>
                    @endif
                    @if($shaafiRequest->sms_sent_at)
                        <p class="mt-2 text-xs text-green-700">
                            <i class="ri-message-2-line"></i> SMS sent {{ $shaafiRequest->sms_sent_at->format('M d, Y H:i') }}
                        </p>
                    @elseif($shaafiRequest->sms_last_error)
                        <p class="mt-2 text-xs text-red-600">SMS failed: {{ $shaafiRequest->sms_last_error }}</p>
                    @endif
                </div>

                <div class="bg-white shadow sm:rounded-lg p-6 border-2 border-blue-100">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Agent Actions</h2>

                    @if(in_array($shaafiRequest->status, ['pending', 'under_review']))
                    <div class="flex flex-col sm:flex-row gap-2 mb-4">
                        <form action="{{ route('shaafi-requests.approve', $shaafiRequest) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="send_sms" value="1">
                            <button type="submit" style="background-color:#16a34a;color:#fff;" class="w-full px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90">
                                <i class="ri-check-line"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('shaafi-requests.reject', $shaafiRequest) }}" method="POST" class="flex-1"
                              onsubmit="return confirm('Reject this request?')">
                            @csrf
                            <button type="submit" style="background-color:#dc2626;color:#fff;" class="w-full px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90">
                                <i class="ri-close-line"></i> Reject
                            </button>
                        </form>
                    </div>
                    @endif

                    <form action="{{ route('shaafi-requests.update-status', $shaafiRequest) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Update Status</label>
                            <select name="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                @foreach(['under_review','approved','rejected','scheduled','completed','cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($shaafiRequest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Schedule Appointment</label>
                            <input type="datetime-local" name="scheduled_at"
                                value="{{ $shaafiRequest->scheduled_at?->format('Y-m-d\TH:i') }}"
                                class="mt-1 w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agent Notes</label>
                            <textarea name="agent_notes" rows="4" class="mt-1 w-full rounded-md border-gray-300 shadow-sm sm:text-sm">{{ old('agent_notes', $shaafiRequest->agent_notes) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Included in the SMS when a schedule time is set.</p>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="hidden" name="send_sms" value="0">
                            <input type="checkbox" name="send_sms" value="1" checked class="rounded border-gray-300 text-blue-600">
                            Send SMS notification to {{ $shaafiRequest->mobile_number }}
                        </label>
                        <p class="text-xs text-gray-500">Uses the same Telesom SMS API as SMS Campaigns.</p>
                        <button type="submit" style="background-color:#2563eb;color:#fff;" class="w-full px-4 py-2 rounded-md text-sm font-semibold hover:opacity-90">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
