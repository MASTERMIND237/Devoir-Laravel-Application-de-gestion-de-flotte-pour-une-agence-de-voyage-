<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'nom'                    => ['required', 'string', 'max:150'],
            'ville_depart'           => ['required', 'string', 'max:100'],
            'ville_arrivee'          => ['required', 'string', 'max:100',
                                         'different:ville_depart'],              // Départ ≠ Arrivée
            'point_depart'           => ['nullable', 'string', 'max:200'],
            'point_arrivee'          => ['nullable', 'string', 'max:200'],
            'distance_km'            => ['required', 'integer', 'min:1'],
            'duree_estimee'          => ['required', 'date_format:H:i:s'],       // Format HH:MM:SS
            'prix_billet'            => ['required', 'numeric', 'min:0'],
            'arrets_intermediaires'  => ['nullable', 'array'],                   // Tableau JSON
            'arrets_intermediaires.*'=> ['string', 'max:100'],                   // Chaque arrêt est une string
            'latitude_depart'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude_depart'       => ['nullable', 'numeric', 'between:-180,180'],
            'latitude_arrivee'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude_arrivee'      => ['nullable', 'numeric', 'between:-180,180'],
            'statut'                 => ['sometimes', Rule::in(['active', 'suspendue', 'supprimee'])],
            'description'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'                 => 'Le nom de la route est obligatoire.',
            'ville_depart.required'        => 'La ville de départ est obligatoire.',
            'ville_arrivee.required'       => 'La ville d\'arrivée est obligatoire.',
            'ville_arrivee.different'      => 'La ville d\'arrivée doit être différente de la ville de départ.',
            'distance_km.required'         => 'La distance est obligatoire.',
            'distance_km.min'              => 'La distance doit être d\'au moins 1 km.',
            'duree_estimee.required'       => 'La durée estimée est obligatoire.',
            'duree_estimee.date_format'    => 'La durée doit être au format HH:MM:SS (ex: 03:30:00).',
            'prix_billet.required'         => 'Le prix du billet est obligatoire.',
            'prix_billet.min'              => 'Le prix ne peut pas être négatif.',
            'arrets_intermediaires.array'  => 'Les arrêts doivent être une liste.',
            'latitude_depart.between'      => 'La latitude doit être entre -90 et 90.',
            'longitude_depart.between'     => 'La longitude doit être entre -180 et 180.',
        ];
    }
}