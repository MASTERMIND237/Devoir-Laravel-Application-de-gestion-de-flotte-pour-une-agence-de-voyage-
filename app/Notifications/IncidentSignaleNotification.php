<?php


// ==========================================================================
// NOTIFICATION 11 : IncidentSignaleNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Rapport;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidentSignaleNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Rapport $rapport) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string { return '🚨 Incident signalé en route'; }

    public function getMessage(): string
    {
        $chauffeur = $this->rapport->driver?->nom_complet ?? 'N/A';
        $trajet    = $this->rapport->affectation?->route?->trajet ?? 'N/A';
        $desc      = $this->rapport->description_incident ?? 'Voir le rapport';
        return "INCIDENT signalé par {$chauffeur} sur la route {$trajet} : {$desc}";
    }

    public function toMailData(): array { return []; }

    public function toDatabaseData(): array
    {
        return [
            'type'       => 'incident_signale',
            'message'    => $this->getMessage(),
            'rapport_id' => $this->rapport->id,
            'priorite'   => 'haute',
            'lien'       => "/satisfy/rapports/{$this->rapport->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->error()
            ->greeting("⚠️ Incident signalé !")
            ->line($this->getMessage())
            ->action('Voir le rapport d\'incident', url("/satisfy/rapports/{$this->rapport->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}

