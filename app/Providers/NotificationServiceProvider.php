<?php

namespace App\Providers;

use App\Models\Affectation;
use App\Models\Maintenance;
use App\Models\Rapport;
use App\Models\Vehicule;
use App\Observers\AffectationObserver;
use App\Observers\MaintenanceObserver;
use App\Observers\RapportObserver;
use App\Observers\VehiculeObserver;
use App\Services\Notifications\NotificationManager;
use Illuminate\Support\ServiceProvider;

/*
|--------------------------------------------------------------------------
| NotificationServiceProvider
|--------------------------------------------------------------------------
|
| Ce provider fait deux choses essentielles au démarrage de l'application :
|
|  1. SINGLETON : Lie NotificationManager comme singleton dans le conteneur
|     IoC de Laravel → une seule instance partagée dans toute l'app.
|
|  2. OBSERVERS : Enregistre les 4 observers sur leurs modèles respectifs
|     → Dès qu'un modèle est créé/modifié, les notifications partent
|       automatiquement sans qu'on ait besoin d'appeler quoi que ce soit
|       dans les controllers.
|
| ENREGISTREMENT : Ajouter ce provider dans config/app.php
|   'providers' => [
|       ...
|       App\Providers\NotificationServiceProvider::class,
|   ],
*/

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Enregistrement du Singleton dans le conteneur IoC
     */
    public function register(): void
    {
        // Liaison Singleton — Laravel retournera toujours la même instance
        // quand on fait app(NotificationManager::class) ou injection de dépendance
        $this->app->singleton(NotificationManager::class, function () {
            return NotificationManager::getInstance();
        });
    }

    /**
     * Enregistrement des Observers
     * Appelé après que tous les services sont enregistrés
     */
    public function boot(): void
    {
        // Chaque modèle est maintenant "surveillé" par son observer
        Affectation::observe(AffectationObserver::class);
        Maintenance::observe(MaintenanceObserver::class);
        Rapport::observe(RapportObserver::class);
        Vehicule::observe(VehiculeObserver::class);
    }
}