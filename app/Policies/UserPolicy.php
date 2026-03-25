<?php

namespace App\Policies;


use App\Models\User;
 
class UserPolicy
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
 
    public function view(User $user, User $model): bool
    {
        // Un user peut voir son propre profil
        // Un gestionnaire peut voir les chauffeurs
        if ($user->isGestionnaire() && $model->isChauffeur()) return true;
        return $user->id === $model->id;
    }
 
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function update(User $user, User $model): bool
    {
        // Un gestionnaire ne peut modifier que les chauffeurs
        if ($user->isGestionnaire()) return $model->isChauffeur();
        // Un user peut modifier son propre profil
        return $user->id === $model->id;
    }
 
    /**
     * Seul un admin peut supprimer un compte
     * Un user ne peut pas se supprimer lui-même
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }
 
    /**
     * Activer/désactiver un compte
     */
    public function toggleActive(User $user, User $model): bool
    {
        // Un gestionnaire peut activer/désactiver les chauffeurs
        if ($user->isGestionnaire()) return $model->isChauffeur();
        return false;
    }
}