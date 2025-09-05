<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureTeamMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Must be logged in
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Must have a related team member record
        // (adjust relation name if yours differs)
        $teamMember = $user->teamMember ?? null;
        if (! $teamMember) {
            // Not a team member — deny
            abort(403, 'You must be a team member to access this area.');
        }

        // Optional: status check (choose the one you use)
        // If you store a string status:
        if (property_exists($teamMember, 'status') && $teamMember->status === 'blocked') {
            abort(403, 'Your team member account is blocked.');
        }

        // If you store a boolean column like is_active:
        if (property_exists($teamMember, 'is_active') && ! $teamMember->is_active) {
            abort(403, 'Your team member account is inactive.');
        }

        return $next($request);
    }
}
