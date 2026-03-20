<?php

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/*
|--------------------------------------------------------------------------
| DESIGN PATTERN : SINGLETON
|--------------------------------------------------------------------------
|
| POURQUOI LE SINGLETON ICI ?
|
| Le NotificationManager est le chef d'orchestre de toutes les
| notifications de l'application. On veut :
|
|  1. UNE SEULE instance dans toute l'application pour éviter
|     d'envoyer des notifications en double.
|
|  2. UN ÉTAT PARTAGÉ : le manager garde un journal de toutes
|     les notifications envoyées (utile pour le débogage et
|     pour éviter les spams).
|
|  3. UNE CONFIGURATION CENTRALISÉE : tous les paramètres
|     (canaux actifs, limites d'envoi) sont gérés en un seul endroit.
|
| COMMENT L'UTILISER :
|   $manager = NotificationManager::getInstance();
|   $manager->envoyer($users, new AffectationCreeeNotification($affectation));
|
| IMPORTANT : En Laravel, on peut aussi lier ce Singleton dans
| AppServiceProvider avec app()->singleton(...) — les deux approches
| sont valides. Ici on implémente le pattern de façon explicite
| pour que ce soit visible dans le code.
*/

class NotificationManager
{
    // ---------------------------------------------------------------
    // SINGLETON — Instance unique
    // ---------------------------------------------------------------

    /**
     * L'unique instance de ce manager
     */
    private static ?self $instance = null;

    /**
     * Journal des notifications envoyées dans cette requête
     * Clé : "UserId_ClassNotification", Valeur : timestamp
     */
    private array $journal = [];

    /**
     * Nombre max de notifications identiques par user par minute
     * (protection anti-spam)
     */
    private int $limiteParMinute = 3;

    /**
     * Constructeur privé — empêche l'instanciation directe avec "new"
     */
    private function __construct()
    {
        // Initialisation privée du manager
    }

    /**
     * Clonage interdit — garantit l'unicité de l'instance
     */
    private function __clone() {}

    /**
     * Désérialisation interdite
     */
    public function __wakeup()
    {
        throw new \Exception('Le NotificationManager ne peut pas être désérialisé.');
    }

    /**
     * Point d'accès global à l'unique instance
     * C'est LA méthode centrale du Singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // ---------------------------------------------------------------
    // MÉTHODES MÉTIER
    // ---------------------------------------------------------------

    /**
     * Envoie une notification à un ou plusieurs users
     *
     * @param User|iterable $destinataires  Un User ou une collection de Users
     * @param mixed         $notification   Une instance de notification Laravel
     */
    public function envoyer(mixed $destinataires, mixed $notification): void
    {
        $users = $destinataires instanceof User
            ? collect([$destinataires])
            : collect($destinataires);

        foreach ($users as $user) {
            // Vérifier la limite anti-spam avant d'envoyer
            if ($this->estLimite($user, $notification)) {
                Log::warning('NotificationManager : limite atteinte', [
                    'user_id'      => $user->id,
                    'notification' => get_class($notification),
                ]);
                continue;
            }

            try {
                $user->notify($notification);
                $this->enregistrerDansJournal($user, $notification);

                Log::info('Notification envoyée', [
                    'user_id'      => $user->id,
                    'user_email'   => $user->email,
                    'notification' => get_class($notification),
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur envoi notification', [
                    'user_id' => $user->id,
                    'erreur'  => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Envoie une notification via le canal "on-demand"
     * (sans user spécifique — utile pour envoyer à une adresse email externe)
     */
    public function envoyerAnonyme(string $email, mixed $notification): void
    {
        try {
            Notification::route('mail', $email)->notify($notification);

            Log::info('Notification anonyme envoyée', [
                'email'        => $email,
                'notification' => get_class($notification),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur notification anonyme', ['erreur' => $e->getMessage()]);
        }
    }

    /**
     * Envoie à tous les gestionnaires et admins de l'application
     */
    public function envoyerAuxGestionnaires(mixed $notification): void
    {
        $gestionnaires = \App\Models\User::whereIn('role', ['admin', 'gestionnaire'])
            ->where('is_active', true)
            ->get();

        $this->envoyer($gestionnaires, $notification);
    }

    /**
     * Retourne le journal des notifications envoyées
     */
    public function getJournal(): array
    {
        return $this->journal;
    }

    /**
     * Vide le journal (utile pour les tests)
     */
    public function viderJournal(): void
    {
        $this->journal = [];
    }

    // ---------------------------------------------------------------
    // MÉTHODES PRIVÉES
    // ---------------------------------------------------------------

    /**
     * Enregistre une notification dans le journal interne
     */
    private function enregistrerDansJournal(User $user, mixed $notification): void
    {
        $cle = $user->id . '_' . get_class($notification);

        if (!isset($this->journal[$cle])) {
            $this->journal[$cle] = [];
        }

        $this->journal[$cle][] = now()->timestamp;
    }

    /**
     * Vérifie si un user a dépassé la limite d'envoi pour ce type de notif
     */
    private function estLimite(User $user, mixed $notification): bool
    {
        $cle = $user->id . '_' . get_class($notification);

        if (!isset($this->journal[$cle])) {
            return false;
        }

        // Compter les envois dans la dernière minute
        $uneMinuteAvant = now()->subMinute()->timestamp;
        $envoisRecents  = array_filter(
            $this->journal[$cle],
            fn($timestamp) => $timestamp >= $uneMinuteAvant
        );

        return count($envoisRecents) >= $this->limiteParMinute;
    }
}