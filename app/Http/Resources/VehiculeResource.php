<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehiculeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identification ---
            'id'               => $this->id,
            'marque'           => $this->marque,
            'modele'           => $this->modele,
            'libelle'          => $this->libelle,                             // Ex: "Toyota Coaster - LT-2345-A"
            'immatriculation'  => $this->immatriculation,
            'annee_fabrication'=> $this->annee_fabrication,
            'type_vehicule'    => $this->type_vehicule,
            'couleur'          => $this->couleur,
            'numero_chassis'   => $this->numero_chassis,
            'carburant'        => $this->carburant,

            // --- Capacité & kilométrage ---
            'capacite_passagers' => $this->capacite_passagers,
            'kilometrage_actuel' => number_format($this->kilometrage_actuel, 0, ',', ' ') . ' km',
            'kilometrage_brut'   => $this->kilometrage_actuel,               // Valeur brute pour les calculs frontend

            // --- Statut ---
            'statut' => $this->statut,

            // --- Documents légaux avec alertes ---
            'assurance' => [
                'date_expiration' => $this->date_expiration_assurance?->format('d/m/Y'),
                'est_expiree'     => $this->assurance_expire,
                'jours_restants'  => $this->date_expiration_assurance
                    ? now()->diffInDays($this->date_expiration_assurance, false)
                    : null,
            ],
            'visite_technique' => [
                'date_expiration' => $this->date_expiration_visite_technique?->format('d/m/Y'),
                'est_expiree'     => $this->visite_technique_expire,
                'jours_restants'  => $this->date_expiration_visite_technique
                    ? now()->diffInDays($this->date_expiration_visite_technique, false)
                    : null,
            ],

            // --- Position GPS ---
            'position' => $this->position,                                   // Accesseur du model → [lat, lng, updated_at]

            // --- Dates ---
            'date_mise_en_service' => $this->date_mise_en_service?->format('d/m/Y'),
            'cree_le'              => $this->created_at->format('d/m/Y'),
            'mis_a_jour_le'        => $this->updated_at->format('d/m/Y'),

            // --- Relations (chargées si disponibles) ---
            'affectation_en_cours' => new AffectationResource(
                $this->whenLoaded('affectationEnCours')
            ),
            'derniere_maintenance' => new MaintenanceResource(
                $this->whenLoaded('derniereMaintenance')
            ),
            'prochaine_maintenance' => new MaintenanceResource(
                $this->whenLoaded('prochaineMaintenance')
            ),
            'affectations' => AffectationResource::collection(
                $this->whenLoaded('affectations')
            ),
            'maintenances' => MaintenanceResource::collection(
                $this->whenLoaded('maintenances')
            ),
            'documents' => DocumentResource::collection(
                $this->whenLoaded('documents')
            ),

            // --- Stats ---
            'stats' => $this->when(
                isset($this->resource->stats),
                fn() => $this->resource->stats
            ),
        ];
    }

    public function with(Request $request): array
    {
        return ['success' => true];
    }
}