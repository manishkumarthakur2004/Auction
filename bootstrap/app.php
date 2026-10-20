<?php

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('admin') || $request->is('admin/*')
                ? route('admin.login')
                : route('user.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            return $request->is('admin') || $request->is('admin/*')
                ? route('admin.dashboard')
                : route('dashboard');
        });
        $middleware->validateCsrfTokens(except: [
            // Logout routes (GET fallback for direct URL navigation)
            'user/logout',
            'logout',
            'admin/logout',
            // Login routes — no authenticated session to protect; prevents 419 after server restart
            'user/login',
            'admin/login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Your session expired. Please try again.');
        });
    })->create();
