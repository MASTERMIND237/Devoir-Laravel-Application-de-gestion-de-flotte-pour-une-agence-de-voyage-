<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RapportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identification ---
            'id'               => $this->id,
            'date_rapport'     => $this->date_rapport?->format('d/m/Y'),
            'statut_validation'=> $this->statut_validation,

            // --- Kilométrage ---
            'kilometrage' => [
                'depart'    => number_format($this->kilometrage_depart, 0, ',', ' ') . ' km',
                'arrivee'   => number_format($this->kilometrage_arrivee, 0, ',', ' ') . ' km',
                'parcouru'  => number_format($this->kilometrage_parcouru, 0, ',', ' ') . ' km',
                'depart_brut'  => $this->kilometrage_depart,
                'arrivee_brut' => $this->kilometrage_arrivee,
                'parcouru_brut'=> $this->kilometrage_parcouru,
            ],

            // --- Carburant ---
            'carburant' => [
                'litres_consommes'    => $this->carburant_consomme
                    ? $this->carburant_consomme . ' L'
                    : null,
                'cout'                => $this->cout_carburant_formate,     // Accesseur : "15 000 FCFA"
                'consommation_100km'  => $this->consommation_aux_100
                    ? $this->consommation_aux_100 . ' L/100km'
                    : null,
            ],

            // --- Passagers ---
            'nombre_passagers_transportes' => $this->nombre_passagers_transportes,

            // --- État du véhicule ---
            'etat_vehicule' => [
                'au_depart'  => $this->etat_vehicule_depart,
                'a_larrivee' => $this->etat_vehicule_arrivee,
            ],

            // --- Incident ---
            'incident' => [
                'signale'     => $this->incident_signale,
                'description' => $this->description_incident,
            ],

            // --- Observations ---
            'observations' => $this->observations,

            // --- Validation ---
            'validation' => [
                'statut'    => $this->statut_validation,
                'valide_le' => $this->valide_at?->format('d/m/Y H:i'),
                'par'       => new UserResource($this->whenLoaded('validateur')),
            ],

            // --- Relations ---
            'affectation' => new AffectationResource($this->whenLoaded('affectation')),
            'driver'      => new DriverResource($this->whenLoaded('driver')),
            'vehicule'    => new VehiculeResource($this->whenLoaded('vehicule')),

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