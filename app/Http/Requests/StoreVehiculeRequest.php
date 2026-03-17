<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'marque'                           => ['required', 'string', 'max:100'],
            'modele'                           => ['required', 'string', 'max:100'],
            'annee_fabrication'                => ['required', 'integer', 'min:1990', 'max:' . date('Y')],
            'immatriculation'                  => ['required', 'string', 'max:20', 'unique:vehicules,immatriculation'],
            'type_vehicule'                    => ['required', Rule::in(['bus', 'minibus', 'berline', 'van'])],
            'capacite_passagers'               => ['required', 'integer', 'min:1', 'max:100'],
            'couleur'                          => ['nullable', 'string', 'max:50'],
            'numero_chassis'                   => ['nullable', 'string', 'max:100', 'unique:vehicules,numero_chassis'],
            'carburant'                        => ['required', Rule::in(['essence', 'diesel', 'hybride', 'electrique'])],
            'kilometrage_actuel'               => ['required', 'integer', 'min:0'],
            'date_mise_en_service'             => ['nullable', 'date', 'before_or_equal:today'],
            'date_expiration_assurance'        => ['nullable', 'date', 'after:today'],
            'date_expiration_visite_technique' => ['nullable', 'date', 'after:today'],
            'statut'                           => ['sometimes', Rule::in(['disponible', 'en_route', 'en_maintenance', 'hors_service'])],
            'notes'                            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'marque.required'                        => 'La marque du véhicule est obligatoire.',
            'modele.required'                        => 'Le modèle est obligatoire.',
            'annee_fabrication.required'             => 'L\'année de fabrication est obligatoire.',
            'annee_fabrication.min'                  => 'L\'année ne peut pas être avant 1990.',
            'annee_fabrication.max'                  => 'L\'année ne peut pas être dans le futur.',
            'immatriculation.required'               => 'La plaque d\'immatriculation est obligatoire.',
            'immatriculation.unique'                 => 'Cette immatriculation est déjà enregistrée.',
            'type_vehicule.required'                 => 'Le type de véhicule est obligatoire.',
            'type_vehicule.in'                       => 'Le type doit être : bus, minibus, berline ou van.',
            'capacite_passagers.required'            => 'La capacité en passagers est obligatoire.',
            'capacite_passagers.min'                 => 'La capacité doit être d\'au moins 1 passager.',
            'carburant.required'                     => 'Le type de carburant est obligatoire.',
            'carburant.in'                           => 'Le carburant doit être : essence, diesel, hybride ou électrique.',
            'kilometrage_actuel.required'            => 'Le kilométrage actuel est obligatoire.',
            'kilometrage_actuel.min'                 => 'Le kilométrage ne peut pas être négatif.',
            'numero_chassis.unique'                  => 'Ce numéro de châssis est déjà enregistré.',
            'date_expiration_assurance.after'        => 'La date d\'expiration de l\'assurance doit être dans le futur.',
            'date_expiration_visite_technique.after' => 'La date d\'expiration de la visite technique doit être dans le futur.',
        ];
    }
}