<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->ip() !== config("api.private.security.Allowed-IP")) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized"
            ], 403);
        }


        return $next($request);
    }
}
