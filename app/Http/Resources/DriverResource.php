<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identité driver ---
            'id'                     => $this->id,
            'numero_permis'          => $this->numero_permis,
            'categorie_permis'       => $this->categorie_permis,
            'date_delivrance_permis' => $this->date_delivrance_permis?->format('d/m/Y'),
            'date_expiration_permis' => $this->date_expiration_permis?->format('d/m/Y'),
            'numero_cni'             => $this->numero_cni,
            'date_naissance'         => $this->date_naissance?->format('d/m/Y'),
            'adresse'                => $this->adresse,
            'ville'                  => $this->ville,
            'annees_experience'      => $this->annees_experience,
            'statut'                 => $this->statut,

            // --- Alertes calculées ---
            'permis_expire'          => $this->permis_expire,               // Accesseur du model (bool)
            'jours_avant_expiration_permis' => $this->date_expiration_permis
                ? now()->diffInDays($this->date_expiration_permis, false)    // Négatif si expiré
                : null,

            // --- User associé ---
            'user' => new UserResource($this->whenLoaded('user')),

            // --- Relations ---
            'affectations' => AffectationResource::collection(
                $this->whenLoaded('affectations')
            ),
            'documents' => DocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            // --- Stats (chargées manuellement dans le controller show()) ---
            'stats' => $this->when(
                isset($this->resource->stats),
                fn() => $this->resource->stats
            ),

            // --- Dates ---
            'cree_le'       => $this->created_at->format('d/m/Y'),
            'mis_a_jour_le' => $this->updated_at->format('d/m/Y'),
        ];
    }

    public function with(Request $request): array
    {
        return ['success' => true];
    }
}