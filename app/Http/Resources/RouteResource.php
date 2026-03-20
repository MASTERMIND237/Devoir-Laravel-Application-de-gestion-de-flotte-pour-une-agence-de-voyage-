<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identification ---
            'id'           => $this->id,
            'nom'          => $this->nom,
            'trajet'       => $this->trajet,                                 // Ex: "Yaoundé → Douala"
            'statut'       => $this->statut,

            // --- Villes ---
            'ville_depart'  => $this->ville_depart,
            'ville_arrivee' => $this->ville_arrivee,
            'point_depart'  => $this->point_depart,
            'point_arrivee' => $this->point_arrivee,

            // --- Métriques ---
            'distance_km'   => $this->distance_km . ' km',
            'distance_brute'=> $this->distance_km,
            'duree_estimee' => $this->duree_estimee,                         // Format HH:MM:SS
            'prix_billet'   => number_format($this->prix_billet, 0, ',', ' ') . ' FCFA',
            'prix_brut'     => $this->prix_billet,

            // --- Arrêts intermédiaires (tableau JSON) ---
            'arrets_intermediaires' => $this->arrets_intermediaires ?? [],

            // --- Coordonnées GPS ---
            'coordonnees' => [
                'depart'  => [
                    'lat' => $this->latitude_depart,
                    'lng' => $this->longitude_depart,
                ],
                'arrivee' => [
                    'lat' => $this->latitude_arrivee,
                    'lng' => $this->longitude_arrivee,
                ],
            ],

            'description'   => $this->description,

            // --- Compteur d'affectations actives ---
            'affectations_actives' => $this->whenCounted('affectationsActives'),

            // --- Relations ---
            'affectations' => AffectationResource::collection(
                $this->whenLoaded('affectations')
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