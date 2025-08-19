<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
	public function handle(Request $request, Closure $next): Response
	{
		// Super Admin can set active hospital via session or query param
		if (auth()->check() && optional(auth()->user()->role)->name === 'super_admin') {
			if ($request->has('as_hospital_id')) {
				$request->session()->put('active_hospital_id', (int) $request->input('as_hospital_id'));
			}
			$tenantId = (int) $request->session()->get('active_hospital_id', 0);
		} else {
			// Regular users: always set their hospital_id as tenant context
			$tenantId = (int) optional(auth()->user())->hospital_id;
		}



		// Always set tenant context if we have a valid hospital_id
		if ($tenantId > 0) {
			app()->instance('tenantId', $tenantId);
		} else {
			// Allow auth/login, logout, dashboard, and super-admin routes without tenant
			$allowed = $request->is('login') || $request->is('logout') || $request->is('dashboard') || $request->is('super-admin*');
			if (!$allowed) {
				abort(403, 'Tenant context not set');
			}
		}

		return $next($request);
	}
} 