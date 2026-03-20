<?php


// ==========================================================================
// NOTIFICATION 10 : RapportRejeteNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Rapport;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RapportRejeteNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Rapport $rapport) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string { return 'Votre rapport a été rejeté ❌'; }

    public function getMessage(): string
    {
        $trajet = $this->rapport->affectation?->route?->trajet ?? 'N/A';
        $motif  = $this->rapport->observations ?? 'Voir les détails';
        return "Votre rapport pour {$trajet} a été rejeté. Motif : {$motif}. Veuillez le corriger et le resoumettre.";
    }

    public function toMailData(): array { return []; }

    public function toDatabaseData(): array
    {
        return [
            'type'       => 'rapport_rejete',
            'message'    => $this->getMessage(),
            'rapport_id' => $this->rapport->id,
            'priorite'   => 'normale',
            'lien'       => "/satisfy/rapports/{$this->rapport->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->error()
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->action('Corriger mon rapport', url("/satisfy/rapports/{$this->rapport->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}

