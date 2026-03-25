<?php

namespace App\Policies;


use App\Models\Driver;
use App\Models\User;
 
class DriverPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
 
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function view(User $user, Driver $driver): bool
    {
        // Gestionnaire → voit tous les drivers
        if ($user->isGestionnaire()) return true;
 
        // Chauffeur → voit uniquement son propre profil
        return $driver->user_id === $user->id;
    }
 
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function update(User $user, Driver $driver): bool
    {
        if ($user->isGestionnaire()) return true;
 
        // Un chauffeur peut modifier certaines infos de son propre profil
        return $driver->user_id === $user->id;
    }
 
    public function delete(User $user, Driver $driver): bool
    {
        return $user->isAdmin();
    }
 
    /**
     * Voir le dashboard personnel (PWA mobile)
     * Uniquement le chauffeur lui-même
     */
    public function voirDashboard(User $user, Driver $driver): bool
    {
        return $driver->user_id === $user->id
            || $user->isGestionnaire();
    }
}
 