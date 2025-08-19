<?php

namespace App\Http\Controllers;

use App\Models\SmsCampaign;
use App\Models\Donor;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmsCampaignController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = SmsCampaign::latest()->paginate(10);
        return view('sms-campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        return view('sms-campaigns.create', compact('bloodTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'message_template' => 'required|string',
            'type' => 'required|in:urgent,scheduled,auto',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = SmsCampaign::create($validated);

        if ($request->type === 'urgent') {
            $this->sendCampaign($campaign);
        }

        return redirect()->route('sms-campaigns.index')
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SmsCampaign $smsCampaign)
    {
        return view('sms-campaigns.show', compact('smsCampaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SmsCampaign $smsCampaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SmsCampaign $smsCampaign)
    {
        //
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(SmsCampaign $smsCampaign)
    {
        //
    }

    public function send(SmsCampaign $smsCampaign)
    {
        try {
            $this->sendCampaign($smsCampaign);
            return redirect()->back()->with('success', 'Campaign sending started.');
        } catch (\Exception $e) {
            Log::error("Failed to start campaign: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to start campaign: ' . $e->getMessage());
        }
    }

    protected function sendCampaign(SmsCampaign $campaign)
    {
        try {
            // Update campaign status to sending
            $campaign->update(['status' => 'sending']);

            // Get recipients
            $recipients = $campaign->getRecipients();
            $totalRecipients = $recipients->count();
            
            // Update total recipients count
            $campaign->update(['total_recipients' => $totalRecipients]);

            if ($totalRecipients === 0) {
                throw new \Exception('No recipients found for this campaign.');
            }

            $successCount = 0;
            $failedCount = 0;

            foreach ($recipients as $donor) {
                try {
                    // Replace placeholders in template
                    $message = str_replace(
                        ['{name}', '{blood_type}'],
                        [$donor->first_name . ' ' . $donor->last_name, $donor->blood_group],
                        $campaign->message_template
                    );

                    // Format phone number
                    $phoneNumber = $this->smsService->formatPhoneNumber($donor->tell);

                    // Send SMS using the SMS service
                    $result = $this->smsService->sendSms($phoneNumber, $message);
                    
                    if ($result['success']) {
                        $successCount++;
                        $campaign->increment('sent_count');
                        Log::info("SMS sent successfully to {$phoneNumber}");
                    } else {
                        $failedCount++;
                        $campaign->increment('failed_count');
                        Log::error("Failed to send SMS to {$phoneNumber}: " . $result['message']);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to {$donor->tell}: " . $e->getMessage());
                    $failedCount++;
                    $campaign->increment('failed_count');
                }
            }

            // Update campaign status
            $campaign->update([
                'status' => ($failedCount === 0) ? 'completed' : 'completed',
                'completed_at' => now()
            ]);

            Log::info("Campaign completed", [
                'campaign_id' => $campaign->id,
                'total' => $totalRecipients,
                'success' => $successCount,
                'failed' => $failedCount
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Campaign failed: " . $e->getMessage());
            $campaign->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Test SMS API connection
     */
    public function testConnection()
    {
        try {
            $result = $this->smsService->testConnection();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS API connection successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'SMS API connection failed: ' . $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMS API connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test SMS
     */
    public function sendTestSms(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:160'
        ]);

        try {
            $phoneNumber = $this->smsService->formatPhoneNumber($request->phone_number);
            $result = $this->smsService->sendSms($phoneNumber, $request->message);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test SMS sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test SMS: ' . $result['message']
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reports()
    {
        $campaigns = SmsCampaign::latest()->paginate(10);
        
        $totalCampaigns = SmsCampaign::count();
        $totalMessagesSent = SmsCampaign::sum('sent_count');
        $totalFailedMessages = SmsCampaign::sum('failed_count');

        return view('sms-campaigns.reports', compact(
            'campaigns',
            'totalCampaigns',
            'totalMessagesSent',
            'totalFailedMessages'
        ));
    }
}
