<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show the global settings page.
     */
    // app/Http/Controllers/SettingsController.php

public function index()
{
    $settings = [
      'App Name'    => config('app.name'),
      'App URL'     => config('app.url'),
      'Mail Sender' => config('mail.from.address'),
      // add more as needed...
    ];

    return view('settings.index', compact('settings'));
}

}
