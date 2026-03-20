<?php


// ==========================================================================
// NOTIFICATION 6 : MaintenanceTermineeNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Maintenance;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceTermineeNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(private Maintenance $maintenance) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string
    {
        return 'Maintenance terminée — Véhicule disponible';
    }

    public function getMessage(): string
    {
        $vehicule = $this->maintenance->vehicule?->libelle ?? 'N/A';
        $cout     = $this->maintenance->cout_formate;
        return "La maintenance de {$vehicule} est terminée. Coût final : {$cout}. Le véhicule est à nouveau disponible.";
    }

    public function toMailData(): array
    {
        return [
            'vehicule'    => $this->maintenance->vehicule?->libelle,
            'cout_final'  => $this->maintenance->cout_formate,
            'pieces'      => $this->maintenance->pieces_remplacees ?? 'Aucune',
            'prochaine'   => $this->maintenance->prochaine_maintenance_date?->format('d/m/Y') ?? 'Non définie',
        ];
    }

    public function toDatabaseData(): array
    {
        return [
            'type'           => 'maintenance_terminee',
            'message'        => $this->getMessage(),
            'maintenance_id' => $this->maintenance->id,
            'vehicle_id'     => $this->maintenance->vehicle_id,
            'cout_final'     => $this->maintenance->cout,
            'lien'           => "/satisfy/vehicules/{$this->maintenance->vehicle_id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $data = $this->toMailData();
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line($this->getMessage())
            ->line("**Pièces remplacées :** {$data['pieces']}")
            ->line("**Prochain entretien :** {$data['prochaine']}")
            ->action('Voir le véhicule', url("/satisfy/vehicules/{$this->maintenance->vehicle_id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}

