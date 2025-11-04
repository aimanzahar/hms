<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and ensure the authenticated user has the required role.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowedRoles = ['doctor', 'patient'];

        if (! in_array($role, $allowedRoles, true)) {
            abort(403, 'Invalid role specification.');
        }

        if ($user->role !== $role) {
            if ($request->expectsJson()) {
                abort(403, 'This action is unauthorized.');
            }

            $fallbackRoute = match (true) {
                $user->isDoctor() => route('doctor.profile'),
                $user->isPatient() => route('patient.profile'),
                default => route('dashboard'),
            };

            return redirect()->intended($fallbackRoute)->with('status', __('You do not have permission to access that resource.'));
        }

        return $next($request);
    }
}