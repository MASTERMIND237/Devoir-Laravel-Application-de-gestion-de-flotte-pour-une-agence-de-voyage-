<?php

use App\Http\Controllers\API\AffectationController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\MaintenanceController;
use App\Http\Controllers\API\RapportController;
use App\Http\Controllers\API\RouteController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VehiculeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Plateforme Gestion de Flotte
|--------------------------------------------------------------------------
| Préfixe global : /satisfy
| Authentification : Laravel Sanctum (token Bearer)
|
| GROUPES :
|  1. Routes publiques     → /satisfy/auth/*
|  2. Routes authentifiées → /satisfy/* (nécessite token)
*/

// ==========================================================================
// PRÉFIXE GLOBAL "satisfy"
// ==========================================================================
Route::prefix('satisfy')->group(function () {

    // ======================================================================
    // 1. ROUTES PUBLIQUES — Pas besoin d'être connecté
    // ======================================================================
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);             // POST /satisfy/auth/login
        Route::post('/register', [AuthController::class, 'register']);         // POST /satisfy/auth/register
    });

    // ======================================================================
    // 2. ROUTES PROTÉGÉES — Nécessite un token Sanctum valide
    // ======================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Auth ---
        Route::prefix('auth')->group(function () {
            Route::post('/logout',     [AuthController::class, 'logout']);      // POST /satisfy/auth/logout
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);  // POST /satisfy/auth/logout-all
            Route::get('/me',          [AuthController::class, 'me']);         // GET  /satisfy/auth/me
        });

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
            Route::get('/dashboard',              [DriverController::class, 'dashboard']); // PWA mobile
            Route::get('/',                       [DriverController::class, 'index']);
            Route::post('/',                      [DriverController::class, 'store']);
            Route::get('/{driver}',               [DriverController::class, 'show']);
            Route::put('/{driver}',               [DriverController::class, 'update']);
            Route::patch('/{driver}',             [DriverController::class, 'update']);
            Route::delete('/{driver}',            [DriverController::class, 'destroy']);
            Route::get('/{driver}/affectations',  [DriverController::class, 'affectations']);
        });

        // --- Véhicules ---
        Route::prefix('vehicules')->group(function () {
            Route::get('/carte',                  [VehiculeController::class, 'carte']);
            Route::get('/alertes',                [VehiculeController::class, 'alertes']);
            Route::get('/',                       [VehiculeController::class, 'index']);
            Route::post('/',                      [VehiculeController::class, 'store']);
            Route::get('/{vehicule}',             [VehiculeController::class, 'show']);
            Route::put('/{vehicule}',             [VehiculeController::class, 'update']);
            Route::patch('/{vehicule}',           [VehiculeController::class, 'update']);
            Route::delete('/{vehicule}',          [VehiculeController::class, 'destroy']);
            Route::patch('/{vehicule}/position',  [VehiculeController::class, 'updatePosition']);
        });

        // --- Routes (itinéraires) ---
        Route::prefix('routes')->group(function () {
            Route::get('/villes',                 [RouteController::class, 'villes']);
            Route::get('/',                       [RouteController::class, 'index']);
            Route::post('/',                      [RouteController::class, 'store']);
            Route::get('/{route}',                [RouteController::class, 'show']);
            Route::put('/{route}',                [RouteController::class, 'update']);
            Route::patch('/{route}',              [RouteController::class, 'update']);
            Route::delete('/{route}',             [RouteController::class, 'destroy']);
        });

        // --- Affectations ---
        Route::prefix('affectations')->group(function () {
            Route::get('/planning',                [AffectationController::class, 'planning']);
            Route::get('/',                        [AffectationController::class, 'index']);
            Route::post('/',                       [AffectationController::class, 'store']);
            Route::get('/{affectation}',           [AffectationController::class, 'show']);
            Route::put('/{affectation}',           [AffectationController::class, 'update']);
            Route::patch('/{affectation}',         [AffectationController::class, 'update']);
            Route::delete('/{affectation}',        [AffectationController::class, 'destroy']);
            Route::patch('/{affectation}/demarrer',[AffectationController::class, 'demarrer']);
            Route::patch('/{affectation}/terminer',[AffectationController::class, 'terminer']);
            Route::patch('/{affectation}/annuler', [AffectationController::class, 'annuler']);
        });

        // --- Maintenances ---
        Route::prefix('maintenances')->group(function () {
            Route::get('/stats',                  [MaintenanceController::class, 'stats']);
            Route::get('/',                       [MaintenanceController::class, 'index']);
            Route::post('/',                      [MaintenanceController::class, 'store']);
            Route::get('/{maintenance}',          [MaintenanceController::class, 'show']);
            Route::put('/{maintenance}',          [MaintenanceController::class, 'update']);
            Route::patch('/{maintenance}',        [MaintenanceController::class, 'update']);
            Route::delete('/{maintenance}',       [MaintenanceController::class, 'destroy']);
        });

        // --- Rapports ---
        Route::prefix('rapports')->group(function () {
            Route::get('/statistiques',           [RapportController::class, 'statistiques']);
            Route::get('/',                       [RapportController::class, 'index']);
            Route::post('/',                      [RapportController::class, 'store']);
            Route::get('/{rapport}',              [RapportController::class, 'show']);
            Route::patch('/{rapport}/valider',    [RapportController::class, 'valider']);
            Route::patch('/{rapport}/rejeter',    [RapportController::class, 'rejeter']);
        });

        // --- Documents ---
        Route::prefix('documents')->group(function () {
            Route::get('/alertes',                [DocumentController::class, 'alertes']);
            Route::get('/',                       [DocumentController::class, 'index']);
            Route::post('/',                      [DocumentController::class, 'store']);
            Route::get('/{document}',             [DocumentController::class, 'show']);
            Route::delete('/{document}',          [DocumentController::class, 'destroy']);
        });
    });
});















// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider and all of them will
// | be assigned to the "api" middleware group. Make something great!
// |
// */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
