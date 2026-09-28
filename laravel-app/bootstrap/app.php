<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booting(function () {
        // The admin login route lives at "admin.login" (prefix "dashbord/app"),
        // not the framework default "login". Point the auth middleware's
        // guest-redirect there so unauthenticated admin requests are
        // redirected gracefully instead of throwing RouteNotFoundException.
        Authenticate::redirectUsing(function ($request) {
            return route('admin.login');
        });
    })
    ->create();
