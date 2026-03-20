<?php

// ==========================================================================
// OBSERVER 2 : MaintenanceObserver
// Surveille les maintenances planifiées et les changements de statut
// ==========================================================================

namespace App\Observers;

use App\Models\Maintenance;
use App\Notifications\MaintenancePlanifieeNotification;
use App\Notifications\MaintenanceTermineeNotification;
use App\Notifications\MaintenanceUrgenceNotification;
use App\Services\Notifications\NotificationManager;

class MaintenanceObserver
{
    private NotificationManager $manager;

    public function __construct()
    {
        $this->manager = NotificationManager::getInstance();
    }

    /**
     * Déclenché après la CRÉATION d'une maintenance
     * → Notifie les gestionnaires qu'une maintenance a été planifiée
     */
    public function created(Maintenance $maintenance): void
    {
        $maintenance->loadMissing('vehicule', 'createur');

        $this->manager->envoyerAuxGestionnaires(
            new MaintenancePlanifieeNotification($maintenance)
        );
    }

    /**
     * Déclenché après la MISE À JOUR d'une maintenance
     * → Surveille le passage au statut "terminee"
     */
    public function updated(Maintenance $maintenance): void
    {
        if (!$maintenance->wasChanged('statut')) {
            return;
        }

        $maintenance->loadMissing('vehicule', 'createur');

        match ($maintenance->statut) {

            // Maintenance terminée → véhicule de nouveau disponible, notifier
            'terminee' => $this->manager->envoyerAuxGestionnaires(
                new MaintenanceTermineeNotification($maintenance)
            ),

            // Maintenance urgente en cours → alerte immédiate
            'en_cours' => $maintenance->type_maintenance === 'moteur' || $maintenance->cout > 500000
                ? $this->manager->envoyerAuxGestionnaires(
                    new MaintenanceUrgenceNotification($maintenance)
                  )
                : null,

            default => null,
        };
    }
}


// ==========================================================================
// OBSERVER 3 : RapportObserver
// Surveille la soumission et la validation des rapports
// ==========================================================================

namespace App\Observers;

use App\Models\Rapport;
use App\Notifications\RapportSoumisNotification;
use App\Notifications\RapportValideNotification;
use App\Notifications\RapportRejeteNotification;
use App\Notifications\IncidentSignaleNotification;
use App\Services\Notifications\NotificationManager;

class RapportObserver
{
    private NotificationManager $manager;

    public function __construct()
    {
        $this->manager = NotificationManager::getInstance();
    }

    /**
     * Déclenché après la CRÉATION d'un rapport
     * → Notifie les gestionnaires qu'un rapport est en attente de validation
     * → Si incident signalé, envoie une alerte urgente
     */
    public function created(Rapport $rapport): void
    {
        $rapport->loadMissing('driver.user', 'vehicule', 'affectation.route');

        // Notification de soumission aux gestionnaires
        $this->manager->envoyerAuxGestionnaires(
            new RapportSoumisNotification($rapport)
        );

        // Alerte immédiate si incident signalé
        if ($rapport->incident_signale) {
            $this->manager->envoyerAuxGestionnaires(
                new IncidentSignaleNotification($rapport)
            );
        }
    }

    /**
     * Déclenché après la MISE À JOUR d'un rapport
     * → Notifie le chauffeur si son rapport est validé ou rejeté
     */
    public function updated(Rapport $rapport): void
    {
        if (!$rapport->wasChanged('statut_validation')) {
            return;
        }

        $rapport->loadMissing('driver.user', 'vehicule');

        match ($rapport->statut_validation) {

            'valide' => $rapport->driver?->user
                ? $this->manager->envoyer(
                    $rapport->driver->user,
                    new RapportValideNotification($rapport)
                  )
                : null,

            'rejete' => $rapport->driver?->user
                ? $this->manager->envoyer(
                    $rapport->driver->user,
                    new RapportRejeteNotification($rapport)
                  )
                : null,

            default => null,
        };
    }
}

