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
        if ($request->getHost() === 'venturedownsouth.com') {
            $newUrl = str_replace(
                'https://venturedownsouth.com',
                'https://venturedownsouth.com.au',
                $request->fullUrl()
            );

            return redirect()->to($newUrl, 301);
        }

        return $next($request);
    }
}
