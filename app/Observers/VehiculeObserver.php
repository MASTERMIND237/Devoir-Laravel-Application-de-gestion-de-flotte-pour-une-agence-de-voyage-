<?php


// ==========================================================================
// OBSERVER 4 : VehiculeObserver
// Surveille les changements critiques sur les véhicules
// ==========================================================================

namespace App\Observers;

use App\Models\Vehicule;
use App\Notifications\VehiculeHorsServiceNotification;
use App\Notifications\DocumentExpirationNotification;
use App\Services\Notifications\NotificationManager;

class VehiculeObserver
{
    private NotificationManager $manager;

    public function __construct()
    {
        $this->manager = NotificationManager::getInstance();
    }

    /**
     * Déclenché après la MISE À JOUR d'un véhicule
     * → Alerte si le véhicule passe en "hors_service"
     * → Alerte si assurance ou visite technique bientôt expirée
     */
    public function updated(Vehicule $vehicule): void
    {
        // Véhicule mis hors service → alerte urgente
        if ($vehicule->wasChanged('statut') && $vehicule->statut === 'hors_service') {
            $this->manager->envoyerAuxGestionnaires(
                new VehiculeHorsServiceNotification($vehicule)
            );
        }

        // Assurance venant d'expirer
        if ($vehicule->wasChanged('date_expiration_assurance') && $vehicule->assurance_expire) {
            $this->manager->envoyerAuxGestionnaires(
                new DocumentExpirationNotification($vehicule, 'assurance')
            );
        }
    }
}