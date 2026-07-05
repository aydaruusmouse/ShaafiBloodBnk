<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_https')) {
            return $next($request);
        }

        if (! $this->isSecure($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    private function isSecure(Request $request): bool
    {
        if ($request->secure()) {
            return true;
        }

        $forwardedProto = $request->headers->get('X-Forwarded-Proto')
            ?? $request->server->get('HTTP_X_FORWARDED_PROTO')
            ?? $request->server->get('X-Forwarded-Proto');

        return strtolower((string) $forwardedProto) === 'https';
    }
}
