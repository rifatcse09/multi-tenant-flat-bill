<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleErrors;
use App\Exceptions\Handler;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleErrors::class,
        ]);

        $middleware->api(append: [
            HandleErrors::class,
        ]);

        $middleware->alias([
            'owner.subscription' => \App\Http\Middleware\EnsureOwnerSubscriptionActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, Handler::class);

return $app;