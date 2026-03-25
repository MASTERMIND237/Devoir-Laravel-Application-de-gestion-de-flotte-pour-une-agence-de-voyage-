<?php

namespace App\Policies;


use App\Models\Rapport;
use App\Models\User;
 
class RapportPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
 
    public function viewAny(User $user): bool
    {
        return true;                            // Filtré par rôle dans le controller
    }
 
    public function view(User $user, Rapport $rapport): bool
    {
        if ($user->isGestionnaire()) return true;
 
        // Un chauffeur ne voit que ses propres rapports
        return $rapport->driver?->user_id === $user->id;
    }
 
    /**
     * Créer/soumettre un rapport
     * Le chauffeur de l'affectation concernée peut soumettre
     */
    public function create(User $user): bool
    {
        return true;                            // Validé plus finement dans StoreRapportRequest
    }
 
    /**
     * Un rapport soumis ne peut pas être modifié directement
     * Il faut le rejeter et en soumettre un nouveau
     */
    public function update(User $user, Rapport $rapport): bool
    {
        return $user->isAdmin();
    }
 
    public function delete(User $user, Rapport $rapport): bool
    {
        return $user->isAdmin();
    }
 
    /**
     * Valider un rapport
     * Seuls admin et gestionnaire valident
     */
    public function valider(User $user, Rapport $rapport): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Rejeter un rapport
     */
    public function rejeter(User $user, Rapport $rapport): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
 
    /**
     * Voir les statistiques kilométriques
     */
    public function voirStatistiques(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
}
 