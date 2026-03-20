<?php


// ==========================================================================
// NOTIFICATION 7 : MaintenanceUrgenceNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Maintenance;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceUrgenceNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Maintenance $maintenance) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }     // Peut inclure 'broadcast' plus tard

    public function getSubject(): string { return '⚠️ Maintenance urgente en cours'; }

    public function getMessage(): string
    {
        $vehicule = $this->maintenance->vehicule?->libelle ?? 'N/A';
        $type     = $this->maintenance->type_maintenance;
        $cout     = $this->maintenance->cout_formate;
        return "ALERTE : Intervention urgente ({$type}) sur {$vehicule}. Coût estimé : {$cout}.";
    }

    public function toMailData(): array { return ['vehicule' => $this->maintenance->vehicule?->libelle]; }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'maintenance_urgence',
            'message'        => $this->getMessage(),
            'maintenance_id' => $this->maintenance->id,
            'priorite'       => 'haute',
            'lien'           => "/satisfy/maintenances/{$this->maintenance->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->error()                                                           // Affiche en rouge dans l'email
            ->greeting("⚠️ Attention {$notifiable->prenom} !")
            ->line($this->getMessage())
            ->action('Voir la maintenance', url("/satisfy/maintenances/{$this->maintenance->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}
