<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            Log::error('Page non trouver', [
                'error' => $e->getMessage(),
                'code' => 404,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Page non trouver',
                    'error' => $e->getMessage(),
                ], 404);
            }
        });
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            Log::error('Données introuvables', [
                'error' => $e->getMessage(),
                'code' => 404,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Données introuvables',
                    'error' => $e->getMessage(),
                ], 404);
            }
        });
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            Log::error('Accès refusé', [
                'error' => $e->getMessage(),
                'code' => 403,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Accès refusé',
                    'error' => $e->getMessage(),
                ], 403);
            }
        });
        $exceptions->render(function (HttpException $e, Request $request) {
            Log::error('Accès refusé', [
                'error' => $e->getMessage(),
                'code' => 403,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Accès refusé',
                    'error' => $e->getMessage(),
                ], 403);
            }
        });
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            Log::error('Méthode non autorisée', [
                'error' => $e->getMessage(),
                'code' => 405,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Méthode non autorisée',
                    'error' => $e->getMessage(),
                ], 405);
            }
        });
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            Log::error('Trop de reqêtes envoyés. Réessayer plus tard.', [
                'error' => $e->getMessage(),
                'code' => 429,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Trop de reqêtes envoyés. Réessayer plus tard.',
                    'error' => $e->getMessage(),
                ], 429);
            }
        });
        $exceptions->render(function (BadMethodCallException $e, Request $request) {
            Log::error('Trop de reqêtes envoyés. Réessayer plus tard.', [
                'message' => "Erreur d’authentification API. Vérifiez la configuration des tokens.",
                'error' => $e->getMessage(),
                'code' => 429,
            ]);
            if (str_contains($e->getMessage(), 'createToken')) {
                return response()->json([
                    'message' => 'Erreur d’authentification API. Vérifiez la configuration des tokens.',
                ], 500);
            }
        });
        $exceptions->render(function (QueryException $e, Request $request) {

            if (!$request->is('api/*')) {
                return null; // laisser Laravel gérer
            }

            $code = $e->errorInfo[1] ?? null;

            Log::error('Database QueryException', [
                'message' => $e->getMessage(),
                'code'    => $code,
                'sql'     => $e->getSql(),
                'bindings'=> $e->getBindings(),
                'url'     => $request->fullUrl(),
                'method'  => $request->method(),
                'ip'      => $request->ip(),
            ]);

            return match ($code) {
                1451 => response()->json([
                    'message' => "Impossible de supprimer ou modifier cette ressource car elle est liée à d'autres données.",
                    'code'    => $code,
                ], 409),

                1054 => response()->json([
                    'message' => "Erreur interne : champ inexistant.",
                    'code'    => $code,
                ], 500),

                default => response()->json([
                    'message' => "Erreur base de données.",
                    'error' => $e->getMessage(),
                    'code'    => $code,
                ], 500),
            };
        });
        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            Log::error('Route non definis ou authentification obligatoire', [
                'error' => $e->getMessage(),
                'code' => 500,
            ]);
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Route non definis ou authentification obligatoire',
                    'error' => $e->getMessage(),
                ], 500);
            }
        });
    })->create();
