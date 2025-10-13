<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Services\TokenService;

class JWTAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function __construct(private TokenService $tokenService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token || !($payload = $this->tokenService->validateToken($token))) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // find the user whose id equals the JWT subject claim
        $request->setUserResolver(fn () => \App\Models\User::find($payload['sub']));
        return $next($request);
    }
}
