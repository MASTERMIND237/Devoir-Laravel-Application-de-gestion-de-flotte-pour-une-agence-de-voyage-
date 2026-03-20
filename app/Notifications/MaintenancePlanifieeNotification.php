<?php

// ==========================================================================
// NOTIFICATION 5 : MaintenancePlanifieeNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Maintenance;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenancePlanifieeNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Maintenance $maintenance) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string
    {
        return 'Maintenance planifiée — ' . $this->maintenance->vehicule?->immatriculation;
    }

    public function getMessage(): string
    {
        $vehicule = $this->maintenance->vehicule?->libelle ?? 'N/A';
        $type     = $this->maintenance->type_maintenance;
        $date     = $this->maintenance->date_maintenance->format('d/m/Y');
        return "Une maintenance ({$type}) est planifiée pour {$vehicule} le {$date}.";
    }

    public function toMailData(): array
    {
        return [
            'vehicule'  => $this->maintenance->vehicule?->libelle,
            'type'      => $this->maintenance->type_maintenance,
            'date'      => $this->maintenance->date_maintenance->format('d/m/Y'),
            'titre'     => $this->maintenance->titre,
            'garage'    => $this->maintenance->garage_prestataire ?? 'Non défini',
            'cout_estime' => $this->maintenance->cout_formate,
        ];
    }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'maintenance_planifiee',
            'message'        => $this->getMessage(),
            'maintenance_id' => $this->maintenance->id,
            'vehicle_id'     => $this->maintenance->vehicle_id,
            'lien'           => "/satisfy/maintenances/{$this->maintenance->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $data = $this->toMailData();
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->line("**Type :** {$data['type']}")
            ->line("**Date :** {$data['date']}")
            ->line("**Garage :** {$data['garage']}")
            ->line("**Coût estimé :** {$data['cout_estime']}")
            ->action('Voir la maintenance', url("/satisfy/maintenances/{$this->maintenance->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}

