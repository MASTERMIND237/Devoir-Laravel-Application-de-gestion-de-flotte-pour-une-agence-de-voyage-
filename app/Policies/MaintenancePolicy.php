<?php

namespace App\Policies;

 
use App\Models\Maintenance;
use App\Models\User;
 
class MaintenancePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
 
    public function viewAny(User $user): bool
    {
        // Les chauffeurs peuvent voir les maintenances de leurs véhicules
        return true;
    }
 
    public function view(User $user, Maintenance $maintenance): bool
    {
        return true;
    }
 
    /**
     * Créer une maintenance
     * Seuls admin et gestionnaire planifient des maintenances
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Supprimer une maintenance
     * Uniquement admin — on ne supprime pas l'historique d'entretien
     */
    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->isAdmin();
    }
 
    /**
     * Voir les statistiques globales de maintenance
     */
    public function voirStats(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
}
 