<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // --- 401 Unauthenticated (missing / invalid token) ---
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 401,
                    'message' => 'Unauthenticated. Token not found or invalid.',
                ], 401);
            }
        });

        // --- 404 Route or Model not found ---
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // If it wraps a ModelNotFoundException, tell which resource
                $previous = $e->getPrevious();
                if ($previous instanceof ModelNotFoundException) {
                    $model = class_basename($previous->getModel());
                    return response()->json([
                        'status'  => 'error',
                        'code'    => 404,
                        'message' => "{$model} with the given ID was not found.",
                    ], 404);
                }

                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'The requested endpoint was not found.',
                ], 404);
            }
        });

        // --- 422 Validation error ---
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // --- 500 Uncaught server error ---
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 500,
                    'message' => 'An internal server error occurred. Please try again later.',
                    // only expose details outside production
                    'detail'  => app()->isProduction() ? null : $e->getMessage(),
                ], 500);
            }
        });

    })->create();
