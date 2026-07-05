<?php

namespace App\Services;

use App\Models\ShaafiRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ShaafiRequestNotificationService
{
    public function __construct(private SmsService $smsService) {}

    public function sendForStatusChange(ShaafiRequest $request, string $event): array
    {
        if (! config('shaafi.sms.enabled', true)) {
            return ['success' => false, 'skipped' => true, 'message' => 'SMS notifications are disabled.'];
        }

        if (empty($request->mobile_number)) {
            return ['success' => false, 'message' => 'No mobile number on this request.'];
        }

        $message = $this->buildMessage($request, $event);

        if ($message === '') {
            return ['success' => false, 'skipped' => true, 'message' => 'No SMS template for this event.'];
        }

        try {
            $phone = $this->smsService->formatPhoneNumber($request->mobile_number);
            $result = $this->smsService->sendSms($phone, $message);

            if ($result['success']) {
                $this->recordSmsResult($request, true);

                Log::info('Shaafi request SMS sent', [
                    'reference' => $request->reference_number,
                    'event' => $event,
                    'phone' => $phone,
                ]);
            } else {
                $this->recordSmsResult($request, false, $result['message'] ?? 'Unknown SMS error');
            }

            return $result;
        } catch (\Throwable $e) {
            $this->recordSmsResult($request, false, $e->getMessage());

            Log::error('Shaafi request SMS failed', [
                'reference' => $request->reference_number,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function resolveEvent(string $status, ?string $scheduledAt): ?string
    {
        if (in_array($status, ['rejected', 'cancelled'], true)) {
            return 'rejected';
        }

        if ($scheduledAt && in_array($status, ['approved', 'scheduled'], true)) {
            return 'scheduled';
        }

        if ($status === 'approved') {
            return 'approved';
        }

        return null;
    }

    private function buildMessage(ShaafiRequest $request, string $event): string
    {
        $template = config("shaafi.sms.templates.{$event}");

        if (! is_string($template) || $template === '') {
            return '';
        }

        $request->loadMissing('hospital');

        $replacements = [
            '{name}' => $request->full_name,
            '{reference}' => $request->reference_number,
            '{blood_group}' => $request->blood_group,
            '{hospital}' => $request->hospital?->name ?? '',
            '{city}' => $request->city,
            '{request_type}' => $request->request_type === 'donation' ? 'donation request' : 'blood request',
            '{scheduled_at}' => $request->scheduled_at?->format('M d, Y H:i') ?? '',
            '{agent_notes}' => trim((string) $request->agent_notes),
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $template);

        return trim(preg_replace('/\s+/', ' ', $message));
    }

    private function recordSmsResult(ShaafiRequest $request, bool $success, ?string $error = null): void
    {
        if (! Schema::hasColumn('shaafi_requests', 'sms_sent_at')) {
            return;
        }

        try {
            $request->update([
                'sms_sent_at' => $success ? now() : $request->sms_sent_at,
                'sms_last_error' => $success ? null : $error,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Could not record Shaafi SMS status', [
                'reference' => $request->reference_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
