<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateShaafiApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = config('shaafi.api_key');

        if (empty($configuredKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Shaafi API integration is not configured on the server.',
            ], 503);
        }

        $providedKey = $request->bearerToken() ?? $request->header('X-API-Key');

        if (! hash_equals($configuredKey, (string) $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API key.',
            ], 401);
        }

        return $next($request);
    }
}
