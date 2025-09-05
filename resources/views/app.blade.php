<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $seo = array_merge(
            [
                'title' => 'Venture Up North',
                'description' => 'Explore, Indulge, Breathe.',
                'image' => asset('images/Venture-Up-North.png'),
                'keywords' =>
                    'south west australia, margaret river, dunsborough, albany, things to do in wa, wine tours, whale watching, Venture Up North',
                'canonical' => 'https://venturedownsouth.com.au' . Str::start(request()->getPathInfo(), '/'),
                'robots' => 'index, follow',
                'type' => 'website',
            ],
            (array) ($page['props']['seo'] ?? []),
        );
    @endphp

    <title inertia>{{ $seo['title'] ?? 'Venture Up North' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Explore, Indulge, Breathe.' }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
    <meta name="keywords"
        content="{{ $seo['keywords'] ?? 'south west australia, margaret river, dunsborough, albany, things to do in wa, wine tours, whale watching, Venture Up North' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? canonical_url() }}">


    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seo['title'] ?? '' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? '' }}">
    <meta property="og:image" content="{{ asset($seo['image']) ?? asset('/public/images/Venture-Up-North.png') }}">
    <meta property="og:url" content="{{ $seo['canonical'] ?? canonical_url() }}">
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? '' }}">
    <meta name="twitter:image" content="{{ asset($seo['image']) ?? asset('/public/images/Venture-Up-North.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- Standard Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('public/images/favicon.ico') }}">
    @if (request()->is('admin*'))
        <meta name="robots" content="noindex, nofollow">
    @else
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-2PRH9X4YN1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'G-2PRH9X4YN1');
        </script>
    @endif
    <!-- Scripts -->
    <script id="mcjs">
        ! function(c, h, i, m, p) {
            m = c.createElement(h), p = c.getElementsByTagName(h)[0], m.async = 1, m.src = i, p.parentNode.insertBefore(m,
                p)
        }(document, "script",
            "https://chimpstatic.com/mcjs-connected/js/users/278c223d88935d8eb67de9227/4ac3938e9e77683b7e3334f73.js");
    </script>
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
