<?php


// ==========================================================================
// NOTIFICATION 12 : VehiculeHorsServiceNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Vehicule;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehiculeHorsServiceNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Vehicule $vehicule) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string { return '🔴 Véhicule mis hors service'; }

    public function getMessage(): string
    {
        return "Le véhicule {$this->vehicule->libelle} a été mis hors service et ne peut plus être affecté.";
    }

    public function toMailData(): array { return ['vehicule' => $this->vehicule->libelle]; }

    public function toDatabaseData(): array
    {
        return [
            'type'       => 'vehicule_hors_service',
            'message'    => $this->getMessage(),
            'vehicle_id' => $this->vehicule->id,
            'priorite'   => 'haute',
            'lien'       => "/satisfy/vehicules/{$this->vehicule->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->error()
            ->line($this->getMessage())
            ->action('Voir le véhicule', url("/satisfy/vehicules/{$this->vehicule->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}

