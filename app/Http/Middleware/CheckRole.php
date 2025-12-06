<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // Check if user has one of the allowed roles
        if (!in_array($userRole, $roles)) {
            // Instead of aborting, redirect to login with error
            // We should also logout the user to ensure they can try again with correct credentials
            auth()->logout();

            return redirect()->route('login')->with('error', 'Unauthorized access. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
