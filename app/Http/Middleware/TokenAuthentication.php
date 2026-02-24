<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');

        if (! $authorization) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => 'Forbidden',
            ], 403);
        }

        $parts = explode(' ', $authorization, 2);
        if (count($parts) !== 2) {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => 'Forbidden',
            ], 403);
        }

        [$scheme, $token] = $parts;

        if (strtolower($scheme) !== 'geonames') {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => 'Forbidden',
            ], 403);
        }

        $tokenConfig = $this->getTokenConfig();

        if (! isset($tokenConfig[$token]) || ($tokenConfig[$token]['status'] ?? '') !== 'A') {
            return response()->json([
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => 'Forbidden',
            ], 403);
        }

        $request->attributes->set('api_user', $tokenConfig[$token]);

        return $next($request);
    }

    private function getTokenConfig(): array
    {
        $configPath = base_path('data/token-config.php');

        if (! file_exists($configPath)) {
            return [];
        }

        return require $configPath;
    }
}
