<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiUrl = 'http://172.16.53.106:8082/sdf/web/sms/otp/send';
    private string $username = 'sms_api';
    private string $password = 'SMS_API@123';
    private string $fromNumber = 'Telesom CRM';
    private string $application = 'Telesom_CRM';

    /**
     * Send SMS using the Telesom API
     *
     * @param string $toNumber Phone number in international format (e.g., +252634160295)
     * @param string $message Message content
     * @param int $timeToLiveSeconds Time to live in seconds (default: 240)
     * @return array Response from API
     * @throws \Exception
     */
    public function sendSms(string $toNumber, string $message, int $timeToLiveSeconds = 240): array
    {
        try {
            // Validate phone number format
            $this->validatePhoneNumber($toNumber);

            // Prepare request payload
            $payload = [
                'fromNumber' => $this->fromNumber,
                'toNumber' => $toNumber,
                'message' => $message,
                'timeToLiveSeconds' => $timeToLiveSeconds,
                'application' => $this->application
            ];

            Log::info('Sending SMS', [
                'to' => $toNumber,
                'message' => $message,
                'payload' => $payload
            ]);

            // Make API request
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->post($this->apiUrl, $payload);

            $responseData = $response->json();
            $statusCode = $response->status();

            Log::info('SMS API Response', [
                'status_code' => $statusCode,
                'response' => $responseData,
                'to' => $toNumber
            ]);

            // Check if request was successful
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $responseData
                ];
            } else {
                throw new \Exception("SMS API Error: HTTP {$statusCode} - " . json_encode($responseData));
            }

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'to' => $toNumber,
                'message' => $message,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk SMS to multiple recipients
     *
     * @param array $recipients Array of phone numbers
     * @param string $message Message content
     * @param int $timeToLiveSeconds Time to live in seconds
     * @return array Results for each recipient
     */
    public function sendBulkSms(array $recipients, string $message, int $timeToLiveSeconds = 240): array
    {
        $results = [];
        $successCount = 0;
        $failedCount = 0;

        foreach ($recipients as $phoneNumber) {
            $result = $this->sendSms($phoneNumber, $message, $timeToLiveSeconds);
            $results[$phoneNumber] = $result;

            if ($result['success']) {
                $successCount++;
            } else {
                $failedCount++;
            }

            // Add a small delay between requests to avoid overwhelming the API
            usleep(100000); // 0.1 second delay
        }

        return [
            'results' => $results,
            'total' => count($recipients),
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ];
    }

    /**
     * Validate phone number format
     *
     * @param string $phoneNumber
     * @throws \Exception
     */
    private function validatePhoneNumber(string $phoneNumber): void
    {
        // Remove any spaces or special characters
        $cleanNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Check if it starts with +252 (Somalia country code)
        if (!preg_match('/^\+252[0-9]{8,9}$/', $cleanNumber)) {
            throw new \Exception("Invalid phone number format. Expected format: +252XXXXXXXXX");
        }
    }

    /**
     * Format phone number to international format
     *
     * @param string $phoneNumber
     * @return string
     */
    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any spaces or special characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If it starts with 252, add +
        if (strpos($cleanNumber, '252') === 0) {
            return '+' . $cleanNumber;
        }

        // If it's a local number (starts with 0), replace with +252
        if (strpos($cleanNumber, '0') === 0) {
            return '+252' . substr($cleanNumber, 1);
        }

        // If it's already in international format, return as is
        if (strpos($cleanNumber, '252') === 0) {
            return '+' . $cleanNumber;
        }

        // Default: assume it's a local number and add +252
        return '+252' . $cleanNumber;
    }

    /**
     * Test the SMS API connection
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(10)
                ->get($this->apiUrl);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
