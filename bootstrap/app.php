<?php

use App\Http\Middleware\RelaxCSP;
use App\Http\Middleware\RoleMiddleware;
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
        $middleware->append(RelaxCSP::class);
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function ($response, Throwable $e, $request) {
            if ($request->isMethod('GET') && !$request->expectsJson()) {
                $getRedirectRoute = function () {
                    $user = auth()->user();
                    if (!$user) {
                        return 'dashboard';
                    }
                    return match ($user->role) {
                        'MITRA' => 'partner.dashboard',
                        'STAF' => 'scan.index',
                        default => 'dashboard',
                    };
                };

                if ($response->getStatusCode() === 403 || $e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return redirect()->route($getRedirectRoute())->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk fitur/halaman tersebut.');
                }

                if ($response->getStatusCode() === 404) {
                    return redirect()->route($getRedirectRoute())->with('error', 'Halaman atau data tidak ditemukan.');
                }

                if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
                    return back()->with('error', 'Ukuran file terlalu besar. Total unggahan melebihi batas sistem (silakan unggah lebih sedikit foto atau perkecil ukuran file).')->withInput();
                }
            }

            return $response;
        });
    })->create();
