<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\DebugCSRF;
use App\Http\Middleware\TrackUserActivity;
use App\Http\Middleware\GlobalRateLimit;
use App\Http\Middleware\MessageRateLimit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state', 'laravel_session']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            DebugCSRF::class,
            TrackUserActivity::class,
            GlobalRateLimit::class,
        ]);

        // Adicionar middleware CSRF para rotas web
        $middleware->validateCsrfTokens(except: [
            'api/csrf-token',
            'broadcasting/auth'
        ]);

        // Register rate limiting middleware aliases
        $middleware->alias([
            'rate.limit' => GlobalRateLimit::class,
            'message.rate.limit' => MessageRateLimit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
