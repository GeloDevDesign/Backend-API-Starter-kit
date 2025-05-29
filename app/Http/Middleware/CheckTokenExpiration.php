<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();

            if ($token->expires_at && $token->expires_at->isPast()) {
                return response()->json(
                    [
                        'message' => 'Token has expired',
                        'error' => 'token_expired'
                    ]
                );
            }
        }

        return $next($request);
    }
}
