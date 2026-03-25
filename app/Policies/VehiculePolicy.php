<?php

namespace App\Policies;


use App\Models\User;
use App\Models\Vehicule;
 
class VehiculePolicy
{
    /**
     * Règle globale : les admins passent toujours
     * Cette méthode est appelée EN PREMIER avant toutes les autres
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;      // Admin → accès total, court-circuit immédiat
        return null;                            // null = continuer vers la méthode spécifique
    }
 
    /**
     * Voir la liste des véhicules
     * Tout le monde connecté peut voir la liste
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
 
    /**
     * Voir un véhicule spécifique
     */
    public function view(User $user, Vehicule $vehicule): bool
    {
        return true;                            // Tout user connecté peut voir un véhicule
    }
 
    /**
     * Créer un véhicule
     * Seuls admin et gestionnaire peuvent enregistrer un nouveau véhicule
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Modifier un véhicule
     */
    public function update(User $user, Vehicule $vehicule): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Supprimer un véhicule
     * Seul l'admin peut supprimer (action critique)
     */
    public function delete(User $user, Vehicule $vehicule): bool
    {
        return $user->isAdmin();
    }
 
    /**
     * Mettre à jour la position GPS
     * Le chauffeur de l'affectation en cours peut mettre à jour la position
     */
    public function updatePosition(User $user, Vehicule $vehicule): bool
    {
        if ($user->isGestionnaire()) return true;
 
        // Vérifier que le chauffeur est bien affecté à ce véhicule
        return $vehicule->affectationEnCours?->driver?->user_id === $user->id;
    }
 
    /**
     * Voir la carte en temps réel
     */
    public function voirCarte(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
}
 