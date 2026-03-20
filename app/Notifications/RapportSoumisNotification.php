<?php


// ==========================================================================
// NOTIFICATION 8 : RapportSoumisNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Rapport;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RapportSoumisNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Rapport $rapport) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database']; }

    public function getSubject(): string { return 'Nouveau rapport en attente de validation'; }

    public function getMessage(): string
    {
        $chauffeur = $this->rapport->driver?->nom_complet ?? 'N/A';
        $km        = $this->rapport->kilometrage_parcouru;
        $trajet    = $this->rapport->affectation?->route?->trajet ?? 'N/A';
        return "{$chauffeur} a soumis un rapport pour {$trajet} ({$km} km parcourus).";
    }

    public function toMailData(): array { return []; }

    public function toDatabaseData(): array
    {
        return [
            'type'       => 'rapport_soumis',
            'message'    => $this->getMessage(),
            'rapport_id' => $this->rapport->id,
            'driver_id'  => $this->rapport->driver_id,
            'km'         => $this->rapport->kilometrage_parcouru,
            'lien'       => "/satisfy/rapports/{$this->rapport->id}",
        ];
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toMail(mixed $notifiable): MailMessage { return new MailMessage; }
}

