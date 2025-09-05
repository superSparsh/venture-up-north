<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\ForceComAuDomain::class,
        ]);
        // ✅ Register your route middleware alias here
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'contributor' => \App\Http\Middleware\EnsureContributor::class,
            'teamMember' => \App\Http\Middleware\EnsureContributor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($e->getStatusCode() === 403 && !$request->expectsJson()) {
                return Inertia::render('Errors/403')->toResponse($request)->setStatusCode(403);
            }

            if ($e->getStatusCode() === 404 && !$request->expectsJson()) {
                return Inertia::render('Errors/404')->toResponse($request)->setStatusCode(404);
            }
        });
    })->create();
