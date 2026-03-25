<?php

use App\Http\Controllers\API\AffectationController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\MaintenanceController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\RapportController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VehiculeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Plateforme Satisfy (VERSION FINALE)
|--------------------------------------------------------------------------
| Préfixe global : /satisfy
| Authentification : Laravel Sanctum
| Autorisation : Policies + RoleMiddleware
*/

Route::prefix('satisfy')->group(function () {

    // ======================================================================
    // 1. ROUTES PUBLIQUES
    // ======================================================================
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    // ======================================================================
    // 2. ROUTES PROTÉGÉES — token Sanctum requis
    // ======================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Auth ---
        Route::prefix('auth')->group(function () {
            Route::post('/logout',     [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::get('/me',          [AuthController::class, 'me']);
        });

        // --- Notifications (tout user connecté) ---
        Route::prefix('notifications')->group(function () {
            Route::get('/',              [NotificationController::class, 'index']);
            Route::get('/non-lues',      [NotificationController::class, 'nonLues']);
            Route::patch('/{id}/lire',   [NotificationController::class, 'marquerLue']);
            Route::patch('/lire-tout',   [NotificationController::class, 'marquerToutesLues']);
            Route::delete('/{id}',       [NotificationController::class, 'destroy']);
        });

        // ------------------------------------------------------------------
        // ROUTES ADMIN + GESTIONNAIRE uniquement
        // ------------------------------------------------------------------
        Route::middleware('role:admin,gestionnaire')->group(function () {

            // --- Users ---
            Route::prefix('users')->group(function () {
                Route::get('/',                       [UserController::class, 'index']);
                Route::post('/',                      [UserController::class, 'store']);
                Route::get('/{user}',                 [UserController::class, 'show']);
                Route::put('/{user}',                 [UserController::class, 'update']);
                Route::patch('/{user}',               [UserController::class, 'update']);
                Route::delete('/{user}',              [UserController::class, 'destroy']);
                Route::patch('/{user}/toggle-active', [UserController::class, 'toggleActive']);
            });

            // --- Drivers ---
            Route::prefix('drivers')->group(function () {
                Route::get('/',                      [DriverController::class, 'index']);
                Route::post('/',                     [DriverController::class, 'store']);
                Route::get('/{driver}',              [DriverController::class, 'show']);
                Route::put('/{driver}',              [DriverController::class, 'update']);
                Route::patch('/{driver}',            [DriverController::class, 'update']);
                Route::delete('/{driver}',           [DriverController::class, 'destroy']);
                Route::get('/{driver}/affectations', [DriverController::class, 'affectations']);
            });

            // --- Véhicules ---
            Route::prefix('vehicules')->group(function () {
                Route::get('/carte',                 [VehiculeController::class, 'carte']);
                Route::get('/alertes',               [VehiculeController::class, 'alertes']);
                Route::get('/',                      [VehiculeController::class, 'index']);
                Route::post('/',                     [VehiculeController::class, 'store']);
                Route::get('/{vehicule}',            [VehiculeController::class, 'show']);
                Route::put('/{vehicule}',            [VehiculeController::class, 'update']);
                Route::patch('/{vehicule}',          [VehiculeController::class, 'update']);
                Route::delete('/{vehicule}',         [VehiculeController::class, 'destroy']);
            });

            // --- Routes (itinéraires) ---
            Route::prefix('routes')->group(function () {
                Route::get('/villes',                [RouteController::class, 'villes']);
                Route::get('/',                      [RouteController::class, 'index']);
                Route::post('/',                     [RouteController::class, 'store']);
                Route::get('/{route}',               [RouteController::class, 'show']);
                Route::put('/{route}',               [RouteController::class, 'update']);
                Route::patch('/{route}',             [RouteController::class, 'update']);
                Route::delete('/{route}',            [RouteController::class, 'destroy']);
            });

            // --- Affectations ---
            Route::prefix('affectations')->group(function () {
                Route::get('/planning',              [AffectationController::class, 'planning']);
                Route::get('/',                      [AffectationController::class, 'index']);
                Route::post('/',                     [AffectationController::class, 'store']);
                Route::get('/{affectation}',         [AffectationController::class, 'show']);
                Route::put('/{affectation}',         [AffectationController::class, 'update']);
                Route::patch('/{affectation}',       [AffectationController::class, 'update']);
                Route::delete('/{affectation}',      [AffectationController::class, 'destroy']);
                Route::patch('/{affectation}/annuler',[AffectationController::class, 'annuler']);
            });

            // --- Maintenances ---
            Route::prefix('maintenances')->group(function () {
                Route::get('/stats',                 [MaintenanceController::class, 'stats']);
                Route::get('/',                      [MaintenanceController::class, 'index']);
                Route::post('/',                     [MaintenanceController::class, 'store']);
                Route::get('/{maintenance}',         [MaintenanceController::class, 'show']);
                Route::put('/{maintenance}',         [MaintenanceController::class, 'update']);
                Route::patch('/{maintenance}',       [MaintenanceController::class, 'update']);
                Route::delete('/{maintenance}',      [MaintenanceController::class, 'destroy']);
            });

            // --- Rapports (validation) ---
            Route::prefix('rapports')->group(function () {
                Route::get('/statistiques',          [RapportController::class, 'statistiques']);
                Route::get('/',                      [RapportController::class, 'index']);
                Route::get('/{rapport}',             [RapportController::class, 'show']);
                Route::patch('/{rapport}/valider',   [RapportController::class, 'valider']);
                Route::patch('/{rapport}/rejeter',   [RapportController::class, 'rejeter']);
            });

            // --- Documents (alertes) ---
            Route::prefix('documents')->group(function () {
                Route::get('/alertes',               [DocumentController::class, 'alertes']);
                Route::get('/',                      [DocumentController::class, 'index']);
                Route::get('/{document}',            [DocumentController::class, 'show']);
                Route::delete('/{document}',         [DocumentController::class, 'destroy']);
            });
        });

        // ------------------------------------------------------------------
        // ROUTES CHAUFFEUR (PWA mobile) — role:chauffeur uniquement
        // ------------------------------------------------------------------
        Route::middleware('role:chauffeur')->group(function () {

            // Dashboard personnel du chauffeur
            Route::get('/drivers/dashboard',         [DriverController::class, 'dashboard']);

            // Mise à jour GPS du véhicule en cours
            Route::patch('/vehicules/{vehicule}/position', [VehiculeController::class, 'updatePosition']);

            // Démarrer / Terminer une affectation
            Route::patch('/affectations/{affectation}/demarrer', [AffectationController::class, 'demarrer']);
            Route::patch('/affectations/{affectation}/terminer', [AffectationController::class, 'terminer']);

            // Voir ses propres affectations
            Route::get('/affectations',              [AffectationController::class, 'index']);
            Route::get('/affectations/{affectation}',[AffectationController::class, 'show']);

            // Soumettre un rapport de kilométrage
            Route::post('/rapports',                 [RapportController::class, 'store']);
            Route::get('/rapports',                  [RapportController::class, 'index']);
            Route::get('/rapports/{rapport}',        [RapportController::class, 'show']);
        });

        // ------------------------------------------------------------------
        // ROUTES PARTAGÉES (tout user connecté)
        // ------------------------------------------------------------------
        Route::post('/documents',                    [DocumentController::class, 'store']);    // Upload document
        Route::get('/routes',                        [RouteController::class, 'index']);       // Liste des routes (publique)
        Route::get('/routes/villes',                 [RouteController::class, 'villes']);      // Liste des villes
    });
});