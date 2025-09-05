<?php

if (!function_exists('canonical_url')) {
    function canonical_url(string $path = null): string
    {
        $base = 'https://ventureupnorth.com.au/';
        $uri = $path ?? request()->getPathInfo();

        return rtrim($base, '/') . '/' . ltrim($uri, '/');
    }
}
