<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();
    //     $request->session()->regenerate();

    //     $user = Auth::user();

    //     if ($user->role === 'admin') {
    //         return redirect()->intended(route('admin.dashboard', absolute: false));
    //     }

    //     // Contributor redirect (fancy URL). Fall back if slug missing.
    //     $slug = optional(optional($user->contributor)->author)->slug;

    //     $target = $slug
    //         ? route('contributors.dashboard', ['slug' => $slug], absolute: false)
    //         : route('dashboard', absolute: false); // or route('contributors.dashboard.me')

    //     return redirect()->intended($target);
    // }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // 🚫 Blocked contributor? kick them out and show error on login
        if ($user?->role !== 'admin' && $user?->contributor?->status === 'blocked') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your contributor account is blocked. Please contact support.',
            ]);
        }

        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Contributor redirect (fancy URL). Fall back if slug missing.
        $slug = optional(optional($user->contributor)->author)->slug;

        $target = $slug
            ? route('contributors.dashboard', ['slug' => $slug], absolute: false)
            : route('dashboard', absolute: false);

        return redirect()->intended($target);
    }



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
