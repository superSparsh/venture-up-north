<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceComAuDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getHost() === 'ventureupnorth.com') {
            $newUrl = str_replace(
                'https://ventureupnorth.com',
                'https://ventureupnorth.com.au',
                $request->fullUrl()
            );

            return redirect()->to($newUrl, 301);
        }

        return $next($request);
    }
}
