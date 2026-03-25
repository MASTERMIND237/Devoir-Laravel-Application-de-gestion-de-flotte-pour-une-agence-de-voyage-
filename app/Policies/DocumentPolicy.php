<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
 
class DocumentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }
 
    public function viewAny(User $user): bool
    {
        return true;
    }
 
    public function view(User $user, Document $document): bool
    {
        if ($user->isGestionnaire()) return true;
 
        // Un chauffeur voit ses propres documents
        // ou les documents d'un véhicule qui lui est affecté
        return $document->uploaded_by === $user->id;
    }
 
    /**
     * Uploader un document
     */
    public function create(User $user): bool
    {
        return true;                            // Tout user connecté peut uploader
    }
 
    public function update(User $user, Document $document): bool
    {
        return $user->isGestionnaire()
            || $document->uploaded_by === $user->id;
    }
 
    /**
     * Supprimer un document
     * L'uploadeur ou un gestionnaire peut supprimer
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->isGestionnaire()
            || $document->uploaded_by === $user->id;
    }
 
    /**
     * Voir les alertes documents expirés
     * Réservé aux gestionnaires
     */
    public function voirAlertes(User $user): bool
    {
        return $user->isAdmin() || $user->isGestionnaire();
    }
}
 