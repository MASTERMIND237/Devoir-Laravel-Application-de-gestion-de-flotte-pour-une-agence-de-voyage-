<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identification ---
            'id'                => $this->id,
            'titre'             => $this->titre,
            'type_maintenance'  => $this->type_maintenance,
            'statut'            => $this->statut,
            'description'       => $this->description,

            // --- Planification ---
            'date_maintenance' => $this->date_maintenance?->format('d/m/Y'),
            'heure_debut'      => $this->heure_debut,
            'heure_fin'        => $this->heure_fin,
            'duree_minutes'    => $this->duree_minutes,                      // Accesseur du model

            // --- Prestataire ---
            'garage_prestataire' => $this->garage_prestataire,

            // --- Coûts ---
            'cout'         => $this->cout_formate,                           // Ex: "45 000 FCFA"
            'cout_brut'    => $this->cout,

            // --- Kilométrage ---
            'kilometrage_a_lintervention' => $this->kilometrage_a_lintervention
                ? number_format($this->kilometrage_a_lintervention, 0, ',', ' ') . ' km'
                : null,
            'prochain_entretien_km' => $this->prochain_entretien_km
                ? number_format($this->prochain_entretien_km, 0, ',', ' ') . ' km'
                : null,
            'prochaine_maintenance_date' => $this->prochaine_maintenance_date?->format('d/m/Y'),

            // --- Pièces & notes ---
            'pieces_remplacees' => $this->pieces_remplacees,
            'notes'             => $this->notes,

            // --- Relations ---
            'vehicule' => new VehiculeResource($this->whenLoaded('vehicule')),
            'createur' => new UserResource($this->whenLoaded('createur')),
            'documents' => DocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            // --- Dates ---
            'cree_le'       => $this->created_at->format('d/m/Y H:i'),
            'mis_a_jour_le' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function with(Request $request): array
    {
        return ['success' => true];
    }
}