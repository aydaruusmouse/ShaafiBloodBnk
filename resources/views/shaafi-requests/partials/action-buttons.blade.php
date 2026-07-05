<div class="flex flex-wrap items-center gap-1">
    <a href="{{ route('shaafi-requests.show', $item) }}"
       style="background-color:#2563eb;color:#fff;"
       class="inline-flex items-center px-2 py-1 rounded text-xs font-medium hover:opacity-90">
        Review
    </a>
    @if(in_array($item->status, ['pending', 'under_review']))
    <form action="{{ route('shaafi-requests.approve', $item) }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="send_sms" value="1">
        <button type="submit"
            style="background-color:#16a34a;color:#fff;"
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium hover:opacity-90"
            onclick="return confirm('Approve {{ $item->reference_number }}?')">
            Approve
        </button>
    </form>
    <form action="{{ route('shaafi-requests.reject', $item) }}" method="POST" class="inline">
        @csrf
        <button type="submit"
            style="background-color:#dc2626;color:#fff;"
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium hover:opacity-90"
            onclick="return confirm('Reject {{ $item->reference_number }}?')">
            Reject
        </button>
    </form>
    @endif
</div>
