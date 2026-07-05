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
];
