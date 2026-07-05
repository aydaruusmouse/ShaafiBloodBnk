<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function ensureTenantContext(): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        if (optional($user->role)->name === 'super_admin') {
            $tenantId = (int) session('active_hospital_id', 0);
            if ($tenantId > 0) {
                app()->instance('tenantId', $tenantId);
            }

            return;
        }

        if ($user->hospital_id) {
            app()->instance('tenantId', $user->hospital_id);
        }
    }
}
