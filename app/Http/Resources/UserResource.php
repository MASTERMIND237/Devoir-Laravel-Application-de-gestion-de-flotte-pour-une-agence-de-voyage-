<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/*
|--------------------------------------------------------------------------
| UserResource
|--------------------------------------------------------------------------
| Formate la réponse JSON d'un User.
|
| POURQUOI LES API RESOURCES ?
|
| Sans Resource, Laravel retourne TOUTES les colonnes du modèle
| y compris les données sensibles (password hashé, remember_token...).
|
| Avec Resource, on contrôle exactement :
|  → Quels champs sont exposés
|  → Sous quel nom (ex: 'nom_complet' calculé à la volée)
|  → Quelles relations sont incluses (et seulement si chargées)
|  → Quels champs sont conditionnels selon le rôle
|
| UTILISATION dans un controller :
|   return new UserResource($user);           // Un seul user
|   return UserResource::collection($users);  // Collection paginée
*/

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identité ---
            'id'          => $this->id,
            'nom'         => $this->nom,
            'prenom'      => $this->prenom,
            'nom_complet' => $this->nom_complet,             // Accesseur du model
            'email'       => $this->email,
            'telephone'   => $this->telephone,
            'role'        => $this->role,
            'is_active'   => $this->is_active,

            // --- Photo ---
            'photo_profil' => $this->photo_profil
                ? asset('storage/' . $this->photo_profil)    // URL complète vers le fichier
                : null,

            // --- Dates ---
            'email_verifie_le' => $this->email_verified_at?->format('d/m/Y H:i'),
            'cree_le'          => $this->created_at->format('d/m/Y H:i'),
            'mis_a_jour_le'    => $this->updated_at->format('d/m/Y H:i'),

            // --- Relation Driver (chargée uniquement si disponible) ---
            // whenLoaded() évite les requêtes N+1 non désirées
            'driver' => new DriverResource($this->whenLoaded('driver')),

            // --- Documents (si chargés) ---
            'documents' => DocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            // --- Champs réservés aux admins ---
            // when() affiche le champ UNIQUEMENT si la condition est vraie
            'deleted_at' => $this->when(
                $request->user()?->isAdmin(),
                $this->deleted_at?->format('d/m/Y H:i')
            ),
        ];
    }

    /**
     * Métadonnées supplémentaires ajoutées à la réponse
     * Apparaissent au niveau racine, en dehors de "data"
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
        ];
    }
}