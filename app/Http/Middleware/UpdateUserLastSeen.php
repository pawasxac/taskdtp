<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Only update last_seen_at if it's null or more than 1 minute ago to reduce DB load
            if (!$user->last_seen_at || (now()->timestamp - $user->last_seen_at->timestamp) >= 60) {
                $user->last_seen_at = now();
                $user->timestamps = false; // Do not touch updated_at
                $user->save();
            }
        }

        return $next($request);
    }
}
