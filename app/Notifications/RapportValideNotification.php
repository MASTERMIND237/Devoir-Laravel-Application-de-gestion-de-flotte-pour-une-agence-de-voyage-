<?php


// ==========================================================================
// NOTIFICATION 9 : RapportValideNotification
// ==========================================================================
 
namespace App\Notifications;
 
use App\Models\Rapport;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
 
class RapportValideNotification extends Notification implements NotificationContract
{
    use Queueable;
 
    public function __construct(private Rapport $rapport) {}
 
    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }
 
    public function getSubject(): string { return 'Votre rapport a été validé ✅'; }
 
    public function getMessage(): string
    {
        $trajet = $this->rapport->affectation?->route?->trajet ?? 'N/A';
        return "Votre rapport de kilométrage pour {$trajet} a été validé par le gestionnaire.";
    }
 
    public function toMailData(): array { return ['trajet' => $this->rapport->affectation?->route?->trajet]; }
 
    public function toDatabaseData(): array
    {
        return [
            'type'       => 'rapport_valide',
            'message'    => $this->getMessage(),
            'rapport_id' => $this->rapport->id,
            'lien'       => "/satisfy/rapports/{$this->rapport->id}",
        ];
    }
 
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->success()
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->action('Voir le rapport', url("/satisfy/rapports/{$this->rapport->id}"));
    }
 
    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}
 