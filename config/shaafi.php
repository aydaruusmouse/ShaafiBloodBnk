<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shaafi App API Key
    |--------------------------------------------------------------------------
    |
    | Shared secret used by the Shaafi mobile app when calling Blood Bank APIs.
    | Send via header: Authorization: Bearer {key}  OR  X-API-Key: {key}
    |
    */
    'api_key' => env('SHAAFI_API_KEY'),

    'blood_groups' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],

    'blood_quantities' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],

    /*
    |--------------------------------------------------------------------------
    | SMS notifications (uses the same Telesom API as SMS Campaigns)
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'enabled' => env('SHAAFI_SMS_ENABLED', true),

        'templates' => [
            'approved' => 'Dear {name}, your {request_type} (Ref: {reference}) has been approved. Please visit {hospital}, {city}. Thank you!',

            'scheduled' => 'Dear {name}, your {request_type} (Ref: {reference}) is approved. Please visit {hospital} on {scheduled_at}. {agent_notes}',

            'rejected' => 'Dear {name}, your request {reference} could not be approved at this time. Please contact {hospital} for assistance.',
        ],
    ],
];
