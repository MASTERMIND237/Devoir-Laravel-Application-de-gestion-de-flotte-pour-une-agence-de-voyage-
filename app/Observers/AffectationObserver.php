<?php

namespace App\Observers;

/*
|--------------------------------------------------------------------------
| DESIGN PATTERN : OBSERVER
|--------------------------------------------------------------------------
|
| POURQUOI L'OBSERVER ICI ?
|
| Le pattern Observer permet à nos modèles Eloquent d'être
| "surveillés" automatiquement. Quand un événement se produit
| sur un modèle (création, mise à jour, suppression), les observers
| enregistrés sont notifiés AUTOMATIQUEMENT sans qu'on ait besoin
| d'appeler manuellement les notifications dans les controllers.
|
| AVANTAGES CONCRETS :
|
|  → Séparation des responsabilités : le controller crée l'affectation,
|    l'observer se charge de notifier. Le controller ne sait même pas
|    que des notifications existent.
|
|  → Réutilisabilité : peu importe d'où vient la création
|    (API, commande artisan, seeder...), la notification parte toujours.
|
|  → Maintenabilité : pour ajouter une nouvelle notification,
|    on modifie uniquement l'observer, pas les controllers.
|
| ENREGISTREMENT (dans AppServiceProvider ou un ObserverServiceProvider) :
|   Affectation::observe(AffectationObserver::class);
|   Maintenance::observe(MaintenanceObserver::class);
|   Rapport::observe(RapportObserver::class);
|   Vehicule::observe(VehiculeObserver::class);
*/

// ==========================================================================
// OBSERVER 1 : AffectationObserver
// Surveille les créations et changements de statut des affectations
// ==========================================================================

namespace App\Observers;

use App\Models\Affectation;
use App\Notifications\AffectationCreeeNotification;
use App\Notifications\AffectationAnnuleeNotification;
use App\Notifications\MissionDemarreeNotification;
use App\Notifications\MissionTermineeNotification;
use App\Services\Notifications\NotificationManager;

class AffectationObserver
{
    private NotificationManager $manager;

    public function __construct()
    {
        // Récupère l'unique instance du Singleton NotificationManager
        $this->manager = NotificationManager::getInstance();
    }

    /**
     * Déclenché automatiquement après la CRÉATION d'une affectation
     * → Notifie le chauffeur qu'une mission lui a été assignée
     */
    public function created(Affectation $affectation): void
    {
        $affectation->loadMissing('driver.user', 'route', 'vehicule');

        // Notifier le chauffeur concerné
        if ($affectation->driver?->user) {
            $this->manager->envoyer(
                $affectation->driver->user,
                new AffectationCreeeNotification($affectation)
            );
        }

        // Notifier aussi les gestionnaires
        $this->manager->envoyerAuxGestionnaires(
            new AffectationCreeeNotification($affectation)
        );
    }

    /**
     * Déclenché automatiquement après la MISE À JOUR d'une affectation
     * → Surveille les changements de statut
     */
    public function updated(Affectation $affectation): void
    {
        // Vérifier si le statut a changé
        if (!$affectation->wasChanged('statut')) {
            return;                                                             // Pas de changement de statut = rien à faire
        }

        $ancienStatut  = $affectation->getOriginal('statut');
        $nouveauStatut = $affectation->statut;

        $affectation->loadMissing('driver.user', 'route', 'vehicule');

        match ($nouveauStatut) {

            // Chauffeur vient de démarrer → notifier les gestionnaires
            'en_cours' => $this->manager->envoyerAuxGestionnaires(
                new MissionDemarreeNotification($affectation)
            ),

            // Mission terminée → notifier le chauffeur et les gestionnaires
            'terminee' => $this->gererMissionTerminee($affectation),

            // Affectation annulée → notifier le chauffeur
            'annulee'  => $affectation->driver?->user
                ? $this->manager->envoyer(
                    $affectation->driver->user,
                    new AffectationAnnuleeNotification($affectation)
                  )
                : null,

            default => null,
        };
    }

    /**
     * Logique spécifique pour la fin de mission
     */
    private function gererMissionTerminee(Affectation $affectation): void
    {
        // Notifier le gestionnaire
        $this->manager->envoyerAuxGestionnaires(
            new MissionTermineeNotification($affectation)
        );

        // Rappeler au chauffeur de soumettre son rapport
        if ($affectation->driver?->user) {
            $this->manager->envoyer(
                $affectation->driver->user,
                new MissionTermineeNotification($affectation)
            );
        }
    }
}