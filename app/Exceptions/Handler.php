<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/*
|--------------------------------------------------------------------------
| Handler.php — Gestionnaire global des exceptions
|--------------------------------------------------------------------------
| Ce fichier existe déjà dans ton projet Laravel (app/Exceptions/Handler.php)
| Tu remplaces simplement son contenu par celui-ci.
|
| RÔLE :
| Intercepter TOUTES les exceptions de l'application et les retourner
| en JSON homogène avec notre structure { success, message, data, errors }
| au lieu du HTML par défaut de Laravel.
|
| POURQUOI C'EST IMPORTANT ?
| Sans ça, ton frontend React reçoit du HTML quand une route n'existe pas,
| du JSON mal formaté quand un modèle est introuvable, etc.
| Avec ça, TOUTES les erreurs ont le même format prévisible.
*/

class Handler extends ExceptionHandler
{
    /**
     * Exceptions qui ne sont jamais reportées dans les logs
     */
    protected $dontReport = [
        //
    ];

    /**
     * Exceptions dont les messages ne sont jamais flashés en session
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Ici on pourrait envoyer les erreurs à Sentry, Bugsnag, etc.
        });
    }

    /**
     * Point d'entrée principal — intercepte toutes les exceptions
     * et les formate en JSON pour les routes API (/satisfy/*)
     */
    public function render($request, Throwable $exception): JsonResponse|\Illuminate\Http\Response
    {
        // Appliquer le format JSON uniquement pour les requêtes API
        if ($this->isApiRequest($request)) {
            return $this->handleApiException($request, $exception);
        }

        // Pour les routes non-API, comportement Laravel par défaut
        return parent::render($request, $exception);
    }

    // =========================================================
    // MÉTHODE PRINCIPALE — Dispatch vers le bon handler
    // =========================================================

    private function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        return match (true) {

            // 401 — Non authentifié (token manquant ou expiré)
            $exception instanceof AuthenticationException
                => $this->respondUnauthorized(),

            // 403 — Accès refusé (mauvais rôle ou Policy refusée)
            $exception instanceof AuthorizationException
                => $this->respondForbidden($exception),

            // 404 — Modèle Eloquent introuvable (ex: Vehicule::findOrFail(999))
            $exception instanceof ModelNotFoundException
                => $this->respondModelNotFound($exception),

            // 404 — Route introuvable
            $exception instanceof NotFoundHttpException
                => $this->respondNotFound(),

            // 405 — Méthode HTTP non autorisée (ex: GET sur une route POST)
            $exception instanceof MethodNotAllowedHttpException
                => $this->respondMethodNotAllowed(),

            // 422 — Erreurs de validation (Form Requests)
            $exception instanceof ValidationException
                => $this->respondValidationError($exception),

            // Erreurs HTTP génériques (403, 404, 500...)
            $exception instanceof HttpException
                => $this->respondHttpException($exception),

            // Erreurs base de données (contrainte FK violée, colonne manquante...)
            $exception instanceof QueryException
                => $this->respondQueryException($exception),

            // Toutes les autres exceptions non prévues
            default => $this->respondServerError($exception),
        };
    }

    // =========================================================
    // RÉPONSES SPÉCIFIQUES PAR TYPE D'ERREUR
    // =========================================================

    /**
     * 401 — Non authentifié
     * Ex: requête sans token Bearer, token expiré
     */
    private function respondUnauthorized(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code'    => 401,
            'message' => 'Non authentifié. Veuillez vous connecter pour accéder à cette ressource.',
            'data'    => null,
        ], 401);
    }

    /**
     * 403 — Accès refusé
     * Ex: chauffeur qui tente d'accéder à une route admin
     */
    private function respondForbidden(AuthorizationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code'    => 403,
            'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires pour cette action.',
            'data'    => null,
        ], 403);
    }

    /**
     * 404 — Modèle Eloquent introuvable
     * Ex: GET /satisfy/vehicules/9999 → Vehicule introuvable
     */
    private function respondModelNotFound(ModelNotFoundException $e): JsonResponse
    {
        // Extraire le nom du modèle pour un message clair
        $model      = $e->getModel();
        $modelName  = class_basename($model);

        // Traduire les noms de modèles en français
        $traductions = [
            'User'        => 'Utilisateur',
            'Driver'      => 'Chauffeur',
            'Vehicule'    => 'Véhicule',
            'Route'       => 'Route',
            'Affectation' => 'Affectation',
            'Maintenance' => 'Maintenance',
            'Rapport'     => 'Rapport',
            'Document'    => 'Document',
        ];

        $nomFr = $traductions[$modelName] ?? $modelName;

        return response()->json([
            'success' => false,
            'code'    => 404,
            'message' => "{$nomFr} introuvable. Vérifiez l'identifiant fourni.",
            'data'    => null,
        ], 404);
    }

    /**
     * 404 — Route introuvable
     * Ex: GET /satisfy/routes-inexistante
     */
    private function respondNotFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code'    => 404,
            'message' => 'La ressource demandée est introuvable.',
            'data'    => null,
        ], 404);
    }

    /**
     * 405 — Méthode HTTP non autorisée
     * Ex: GET sur /satisfy/affectations/{id}/demarrer (qui attend PATCH)
     */
    private function respondMethodNotAllowed(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code'    => 405,
            'message' => 'Méthode HTTP non autorisée pour cette route.',
            'data'    => null,
        ], 405);
    }

    /**
     * 422 — Erreurs de validation
     * Ex: Form Request échoue → retourne les erreurs de validation en français
     *
     * Structure retournée :
     * {
     *   "success": false,
     *   "code": 422,
     *   "message": "Les données fournies sont invalides.",
     *   "errors": {
     *     "email": ["L'adresse email est obligatoire."],
     *     "immatriculation": ["Cette immatriculation est déjà enregistrée."]
     *   }
     * }
     */
    private function respondValidationError(ValidationException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code'    => 422,
            'message' => 'Les données fournies sont invalides.',
            'errors'  => $e->errors(),              // Tableau des erreurs par champ
            'data'    => null,
        ], 422);
    }

    /**
     * Exceptions HTTP génériques
     * Ex: abort(403), abort(404), abort(500)
     */
    private function respondHttpException(HttpException $e): JsonResponse
    {
        $statusCode = $e->getStatusCode();
        $message    = $e->getMessage() ?: $this->getHttpMessage($statusCode);

        return response()->json([
            'success' => false,
            'code'    => $statusCode,
            'message' => $message,
            'data'    => null,
        ], $statusCode);
    }

    /**
     * 500 — Erreur base de données
     * Ex: contrainte de clé étrangère violée, colonne inexistante
     *
     * IMPORTANT : En production, on ne retourne PAS le message SQL brut
     * car il peut contenir des infos sensibles sur la structure de la BD.
     */
    private function respondQueryException(QueryException $e): JsonResponse
    {
        // En développement : message détaillé pour débugger
        if (config('app.debug')) {
            return response()->json([
                'success' => false,
                'code'    => 500,
                'message' => 'Erreur base de données : ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }

        // Détecter les erreurs de contrainte FK (ex: supprimer un véhicule qui a des affectations)
        if ($e->getCode() === '23000') {
            return response()->json([
                'success' => false,
                'code'    => 409,
                'message' => 'Impossible d\'effectuer cette opération : des données liées existent encore. '
                           . 'Supprimez d\'abord les éléments associés.',
                'data'    => null,
            ], 409);
        }

        return response()->json([
            'success' => false,
            'code'    => 500,
            'message' => 'Une erreur est survenue avec la base de données. Réessayez.',
            'data'    => null,
        ], 500);
    }

    /**
     * 500 — Erreur serveur inattendue
     * Ex: PHP fatal error, exception non prévue
     */
    private function respondServerError(Throwable $e): JsonResponse
    {
        // En développement : message complet pour débugger
        if (config('app.debug')) {
            return response()->json([
                'success'   => false,
                'code'      => 500,
                'message'   => $e->getMessage(),
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => collect($e->getTrace())->take(5)->toArray(), // 5 premières lignes du stack trace
                'data'      => null,
            ], 500);
        }

        // En production : message générique (ne pas exposer les détails internes)
        return response()->json([
            'success' => false,
            'code'    => 500,
            'message' => 'Une erreur interne est survenue. Notre équipe a été notifiée.',
            'data'    => null,
        ], 500);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Vérifie si la requête vient de notre API /satisfy/*
     */
    private function isApiRequest(Request $request): bool
    {
        return $request->is('satisfy/*')
            || $request->expectsJson()
            || $request->is('api/*');
    }

    /**
     * Messages HTTP standards en français
     */
    private function getHttpMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Requête invalide.',
            401 => 'Non authentifié.',
            403 => 'Accès refusé.',
            404 => 'Ressource introuvable.',
            405 => 'Méthode non autorisée.',
            408 => 'Délai d\'attente dépassé.',
            409 => 'Conflit avec l\'état actuel de la ressource.',
            422 => 'Données invalides.',
            429 => 'Trop de requêtes. Veuillez patienter.',
            500 => 'Erreur interne du serveur.',
            502 => 'Passerelle incorrecte.',
            503 => 'Service temporairement indisponible.',
            default => 'Une erreur est survenue.',
        };
    }
}