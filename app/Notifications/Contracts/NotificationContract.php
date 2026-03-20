<?php

namespace App\Notifications\Contracts;

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Interface NotificationContract
|--------------------------------------------------------------------------
| Design Pattern : STRATEGY + TEMPLATE METHOD
|
| Pourquoi ?
| Toutes nos notifications (email, SMS, database) doivent respecter
| le même contrat. On peut ainsi les interchanger sans toucher
| au reste du code. C'est le principe Open/Closed (SOLID).
|
| Chaque notification concrète DOIT implémenter ces méthodes.
*/

interface NotificationContract
{
    /**
     * Retourne le sujet/titre de la notification
     */
    public function getSubject(): string;

    /**
     * Retourne le message principal
     */
    public function getMessage(): string;

    /**
     * Retourne les canaux de diffusion
     * Ex: ['mail', 'database', 'broadcast']
     */
    public function getChannels(): array;

    /**
     * Retourne les données pour la vue email
     */
    public function toMailData(): array;

    /**
     * Retourne les données pour la base de données
     */
    public function toDatabaseData(): array;
}