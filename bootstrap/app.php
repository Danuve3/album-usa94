<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh the page.'], 419);
            }

            $isAdminRoute = str_starts_with($request->path(), 'admin');
            $loginRoute = $isAdminRoute ? backpack_url('login') : route('login');

            return redirect($loginRoute)
                ->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
        });
    })->create();
