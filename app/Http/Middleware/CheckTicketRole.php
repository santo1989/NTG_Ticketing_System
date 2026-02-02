<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTicketRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles = '')
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has a role assigned
        if (!$user->role) {
            abort(403, 'User has no assigned role');
        }

        $userRole = $user->role->name;
        $allowedRoles = explode(',', $roles);
        $allowedRoles = array_map('trim', $allowedRoles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, "Unauthorized access. Your role '{$userRole}' is not allowed. Required roles: " . implode(', ', $allowedRoles));
        }

        return $next($request);
    }
}
