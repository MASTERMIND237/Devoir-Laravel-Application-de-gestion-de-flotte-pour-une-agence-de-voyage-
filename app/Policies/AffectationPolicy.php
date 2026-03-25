<?php

namespace App\Policies;

 
use App\Models\Affectation;
use App\Models\User;
 
class AffectationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
 
    public function viewAny(User $user): bool
    {
        return true;                            // Tout le monde voit les affectations (filtrées par controller)
    }
 
    public function view(User $user, Affectation $affectation): bool
    {
        if ($user->isGestionnaire()) return true;
 
        // Chauffeur → voit seulement ses propres affectations
        return $affectation->driver?->user_id === $user->id;
    }
 
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function update(User $user, Affectation $affectation): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    public function delete(User $user, Affectation $affectation): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Démarrer une affectation
     * Le chauffeur assigné OU un gestionnaire
     */
    public function demarrer(User $user, Affectation $affectation): bool
    {
        if ($user->isGestionnaire()) return true;
        return $affectation->driver?->user_id === $user->id;
    }
 
    /**
     * Terminer une affectation
     */
    public function terminer(User $user, Affectation $affectation): bool
    {
        if ($user->isGestionnaire()) return true;
        return $affectation->driver?->user_id === $user->id;
    }
 
    /**
     * Annuler une affectation
     * Seuls admin et gestionnaire peuvent annuler
     */
    public function annuler(User $user, Affectation $affectation): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
}