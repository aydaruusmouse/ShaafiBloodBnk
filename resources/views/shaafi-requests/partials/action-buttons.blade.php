<div class="flex flex-col gap-2">
    <a href="{{ route('shaafi-requests.show', $item) }}"
       style="background-color:#2563eb;color:#fff;"
       class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-md text-xs font-semibold hover:opacity-90">
        <i class="ri-eye-line"></i> Review
    </a>
    @if(in_array($item->status, ['pending', 'under_review']))
    <div class="flex gap-2">
        <form action="{{ route('shaafi-requests.approve', $item) }}" method="POST" class="flex-1">
            @csrf
            <input type="hidden" name="send_sms" value="1">
            <button type="submit"
                style="background-color:#16a34a;color:#fff;"
                class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 rounded-md text-xs font-semibold hover:opacity-90"
                onclick="return confirm('Approve {{ $item->reference_number }}?')">
                <i class="ri-check-line"></i> Approve
            </button>
        </form>
        <form action="{{ route('shaafi-requests.reject', $item) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit"
                style="background-color:#dc2626;color:#fff;"
                class="w-full inline-flex items-center justify-center gap-1 px-3 py-2 rounded-md text-xs font-semibold hover:opacity-90"
                onclick="return confirm('Reject {{ $item->reference_number }}?')">
                <i class="ri-close-line"></i> Reject
            </button>
        </form>
    </div>
    @endif
</div>
