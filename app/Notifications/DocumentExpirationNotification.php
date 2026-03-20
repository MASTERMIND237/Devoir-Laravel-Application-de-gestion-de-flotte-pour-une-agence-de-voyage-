<?php

// ==========================================================================
// NOTIFICATION 13 : DocumentExpirationNotification
// ==========================================================================

namespace App\Notifications;

use App\Models\Vehicule;
use App\Notifications\Contracts\NotificationContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentExpirationNotification extends Notification implements NotificationContract
{
    use Queueable;

    public function __construct(
        private Vehicule $vehicule,
        private string   $typeDocument
    ) {}

    public function via(mixed $notifiable): array { return $this->getChannels(); }
    public function getChannels(): array { return ['database', 'mail']; }

    public function getSubject(): string
    {
        return "⚠️ {$this->typeDocument} expirée — {$this->vehicule->immatriculation}";
    }

    public function getMessage(): string
    {
        return "L'{$this->typeDocument} du véhicule {$this->vehicule->libelle} a expiré. Veuillez la renouveler.";
    }

    public function toMailData(): array { return []; }

    public function toDatabaseData(): array
    {
        return [
            'type'          => 'document_expiration',
            'message'       => $this->getMessage(),
            'vehicle_id'    => $this->vehicule->id,
            'type_document' => $this->typeDocument,
            'priorite'      => 'haute',
            'lien'          => "/satisfy/vehicules/{$this->vehicule->id}",
        ];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->error()
            ->line($this->getMessage())
            ->action('Mettre à jour les documents', url("/satisfy/vehicules/{$this->vehicule->id}"));
    }

    public function toDatabase(mixed $notifiable): array { return $this->toDatabaseData(); }
    public function toArray(mixed $notifiable): array { return $this->toDatabaseData(); }
}