<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffectationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // --- Identification ---
            'id'     => $this->id,
            'statut' => $this->statut,
            'resume' => $this->resume,                                       // Accesseur : "Jean | Toyota | Yaounde→Douala | 06/03/2025"

            // --- Planification ---
            'date_depart'          => $this->date_depart?->format('d/m/Y'),
            'heure_depart'         => $this->heure_depart,
            'date_arrivee_prevue'  => $this->date_arrivee_prevue?->format('d/m/Y'),
            'heure_arrivee_prevue' => $this->heure_arrivee_prevue,

            // --- Temps réels (remplis par le chauffeur) ---
            'depart_effectif'   => $this->depart_effectif?->format('d/m/Y H:i'),
            'arrivee_effective' => $this->arrivee_effective?->format('d/m/Y H:i'),
            'duree_reelle'      => $this->duree_reelle_minutes
                ? $this->formaterDuree($this->duree_reelle_minutes)
                : null,

            // --- Passagers ---
            'nombre_passagers' => $this->nombre_passagers,

            // --- Observations ---
            'observations' => $this->observations,

            // --- Relations ---
            'driver'   => new DriverResource($this->whenLoaded('driver')),
            'vehicule' => new VehiculeResource($this->whenLoaded('vehicule')),
            'route'    => new RouteResource($this->whenLoaded('route')),
            'rapport'  => new RapportResource($this->whenLoaded('rapport')),
            'createur' => new UserResource($this->whenLoaded('createur')),

            // --- Dates ---
            'cree_le'       => $this->created_at->format('d/m/Y H:i'),
            'mis_a_jour_le' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Convertit des minutes en format lisible
     * Ex: 185 → "3h 05min"
     */
    private function formaterDuree(int $minutes): string
    {
        $heures = intdiv($minutes, 60);
        $mins   = $minutes % 60;

        if ($heures > 0) {
            return "{$heures}h " . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'min';
        }

        return "{$mins} min";
    }

    public function with(Request $request): array
    {
        return ['success' => true];
    }
}