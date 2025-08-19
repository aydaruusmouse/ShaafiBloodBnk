<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class TestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone : Phone number to send test SMS to} {message? : Test message content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS service by sending a test message';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService)
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'Test message from Blood Bank Management System';

        $this->info("Testing SMS service...");
        $this->info("Phone: {$phone}");
        $this->info("Message: {$message}");

        try {
            // Format phone number
            $formattedPhone = $smsService->formatPhoneNumber($phone);
            $this->info("Formatted phone: {$formattedPhone}");

            // Send SMS
            $result = $smsService->sendSms($formattedPhone, $message);

            if ($result['success']) {
                $this->info("✅ SMS sent successfully!");
                $this->info("Response: " . json_encode($result['data']));
            } else {
                $this->error("❌ SMS sending failed!");
                $this->error("Error: " . $result['message']);
            }

        } catch (\Exception $e) {
            $this->error("❌ Exception occurred: " . $e->getMessage());
        }

        return 0;
    }
}
