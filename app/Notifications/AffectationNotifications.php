<?php

// ==========================================================================
// NOTIFICATION 1 : AffectationCreeeNotification
// Envoyée au chauffeur quand une mission lui est assignée
// ==========================================================================

namespace App\Notifications;

use App\Models\Affectation;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffectationCreeeNotification extends Notification implements NotificationContract
{
    use Queueable;                                                              // La notif est mise en file d'attente (asynchrone)

    public function __construct(private Affectation $affectation) {}

    /**
     * Canaux utilisés : base de données + email
     */
    public function via(mixed $notifiable): array
    {
        return $this->getChannels();
    }

    public function getChannels(): array
    {
        return ['database', 'mail'];
    }

    public function getSubject(): string
    {
        return 'Nouvelle mission assignée — ' . $this->affectation->route?->trajet;
    }

    public function getMessage(): string
    {
        $date   = $this->affectation->date_depart->format('d/m/Y');
        $heure  = $this->affectation->heure_depart;
        $trajet = $this->affectation->route?->trajet ?? 'N/A';
        $bus    = $this->affectation->vehicule?->libelle ?? 'N/A';

        return "Une nouvelle mission vous a été assignée : {$trajet} le {$date} à {$heure} avec le véhicule {$bus}.";
    }

    public function toMailData(): array
    {
        return [
            'trajet'          => $this->affectation->route?->trajet,
            'date_depart'     => $this->affectation->date_depart->format('d/m/Y'),
            'heure_depart'    => $this->affectation->heure_depart,
            'vehicule'        => $this->affectation->vehicule?->libelle,
            'immatriculation' => $this->affectation->vehicule?->immatriculation,
        ];
    }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'affectation_creee',
            'message'        => $this->getMessage(),
            'affectation_id' => $this->affectation->id,
            'route'          => $this->affectation->route?->trajet,
            'date_depart'    => $this->affectation->date_depart->toDateString(),
            'heure_depart'   => $this->affectation->heure_depart,
            'vehicule_id'    => $this->affectation->vehicle_id,
            'lien'           => "/satisfy/affectations/{$this->affectation->id}",
        ];
    }

    /**
     * Contenu de l'email (canal mail)
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $data = $this->toMailData();

        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Bonjour {$notifiable->prenom} !")
            ->line($this->getMessage())
            ->line("**Trajet :** {$data['trajet']}")
            ->line("**Date :** {$data['date_depart']} à {$data['heure_depart']}")
            ->line("**Véhicule :** {$data['vehicule']} ({$data['immatriculation']})")
            ->action('Voir l\'affectation', url("/satisfy/affectations/{$this->affectation->id}"))
            ->line('Bonne route et conduisez prudemment !');
    }

    /**
     * Contenu pour la table notifications (canal database)
     */
    public function toDatabase(mixed $notifiable): array
    {
        return $this->toDatabaseData();
    }

    public function toArray(mixed $notifiable): array
    {
        return $this->toDatabaseData();
    }
}


// ==========================================================================
// NOTIFICATION 2 : AffectationAnnuleeNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Affectation;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffectationAnnuleeNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Affectation $affectation) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string
    {
        return 'Mission annulée — ' . $this->affectation->route?->trajet;
    }

    public function getMessage(): string
    {
        $date   = $this->affectation->date_depart->format('d/m/Y');
        $trajet = $this->affectation->route?->trajet ?? 'N/A';
        return "Votre mission du {$date} ({$trajet}) a été annulée.";
    }

    public function toMailData(): array
    {
        return [
            'trajet'     => $this->affectation->route?->trajet,
            'date'       => $this->affectation->date_depart->format('d/m/Y'),
            'raison'     => $this->affectation->observations ?? 'Non précisée',
        ];
    }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'affectation_annulee',
            'message'        => $this->getMessage(),
            'affectation_id' => $this->affectation->id,
            'lien'           => "/satisfy/affectations/{$this->affectation->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $data = $this->toMailData();
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->line("**Raison :** {$data['raison']}")
            ->line('Contactez votre gestionnaire pour plus d\'informations.')
            ->action('Voir mes affectations', url('/satisfy/affectations'));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}


// ==========================================================================
// NOTIFICATION 3 : MissionDemarreeNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Affectation;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionDemarreeNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Affectation $affectation) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database']; }              // Base de données uniquement (temps réel)

    public function getSubject(): string { return 'Mission démarrée'; }

    public function getMessage(): string
    {
        $chauffeur = $this->affectation->driver?->nom_complet ?? 'N/A';
        $trajet    = $this->affectation->route?->trajet ?? 'N/A';
        $heure     = now()->format('H:i');
        return "{$chauffeur} a démarré sa mission {$trajet} à {$heure}.";
    }

    public function toMailData(): array { return []; }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'mission_demarree',
            'message'        => $this->getMessage(),
            'affectation_id' => $this->affectation->id,
            'chauffeur'      => $this->affectation->driver?->nom_complet,
            'vehicule'       => $this->affectation->vehicule?->immatriculation,
            'trajet'         => $this->affectation->route?->trajet,
            'heure_depart'   => now()->toTimeString(),
            'lien'           => "/satisfy/affectations/{$this->affectation->id}",
        ];
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toMail(mixed $notifiable): MailMessage { return new MailMessage; }
}


// ==========================================================================
// NOTIFICATION 4 : MissionTermineeNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Affectation;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissionTermineeNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Affectation $affectation) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string
    {
        return 'Mission terminée — Soumettez votre rapport';
    }

    public function getMessage(): string
    {
        $trajet = $this->affectation->route?->trajet ?? 'N/A';
        return "Votre mission {$trajet} est terminée. Pensez à soumettre votre rapport de kilométrage.";
    }

    public function toMailData(): array
    {
        return [
            'trajet'         => $this->affectation->route?->trajet,
            'affectation_id' => $this->affectation->id,
        ];
    }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'mission_terminee',
            'message'        => $this->getMessage(),
            'affectation_id' => $this->affectation->id,
            'lien'           => "/satisfy/rapports/create?affectation={$this->affectation->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->action('Soumettre mon rapport', url("/satisfy/rapports/create?affectation={$this->affectation->id}"))
            ->line('Merci de soumettre votre rapport dans les 24 heures.');
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}