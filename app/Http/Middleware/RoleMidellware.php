<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMidellware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$role)
    {
        $userRole = $request->user()->role;
        if (in_array($userRole, $role)) {
            return $next($request);
        }
        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
}
