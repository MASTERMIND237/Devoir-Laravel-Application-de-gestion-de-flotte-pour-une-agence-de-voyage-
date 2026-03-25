<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| RoleMiddleware
|--------------------------------------------------------------------------
| Middleware de vérification des rôles.
| Complète Sanctum en vérifiant le RÔLE après l'authentification.
|
| UTILISATION dans api.php :
|
|   // Route accessible uniquement aux admins
|   Route::middleware(['auth:sanctum', 'role:admin'])->group(...)
|
|   // Route accessible aux admins ET gestionnaires
|   Route::middleware(['auth:sanctum', 'role:admin,gestionnaire'])->group(...)
|
|   // Route accessible à tous les rôles
|   Route::middleware(['auth:sanctum', 'role:admin,gestionnaire,chauffeur'])->group(...)
|
| ENREGISTREMENT dans bootstrap/app.php (Laravel 10 : dans Kernel.php) :
|   protected $routeMiddleware = [
|       'role' => \App\Http\Middleware\RoleMiddleware::class,
|   ];
*/

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est connecté
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié. Veuillez vous connecter.',
            ], 401);
        }

        // Vérifier que le compte est actif
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est suspendu. Contactez l\'administrateur.',
            ], 403);
        }

        // Vérifier que le rôle de l'utilisateur est dans la liste autorisée
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.',
                'votre_role'  => $user->role,
                'roles_requis'=> $roles,
            ], 403);
        }

        return $next($request);
    }
}