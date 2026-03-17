<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        $vehiculeId = $this->route('vehicule')->id;

        return [
            'marque'                           => ['sometimes', 'string', 'max:100'],
            'modele'                           => ['sometimes', 'string', 'max:100'],
            'annee_fabrication'                => ['sometimes', 'integer', 'min:1990', 'max:' . date('Y')],
            'immatriculation'                  => ['sometimes', 'string', 'max:20',
                                                   Rule::unique('vehicules', 'immatriculation')->ignore($vehiculeId)],
            'type_vehicule'                    => ['sometimes', Rule::in(['bus', 'minibus', 'berline', 'van'])],
            'capacite_passagers'               => ['sometimes', 'integer', 'min:1', 'max:100'],
            'couleur'                          => ['sometimes', 'nullable', 'string', 'max:50'],
            'numero_chassis'                   => ['sometimes', 'nullable', 'string', 'max:100',
                                                   Rule::unique('vehicules', 'numero_chassis')->ignore($vehiculeId)],
            'carburant'                        => ['sometimes', Rule::in(['essence', 'diesel', 'hybride', 'electrique'])],
            'kilometrage_actuel'               => ['sometimes', 'integer', 'min:0'],
            'date_mise_en_service'             => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'date_expiration_assurance'        => ['sometimes', 'nullable', 'date'],
            'date_expiration_visite_technique' => ['sometimes', 'nullable', 'date'],
            'statut'                           => ['sometimes', Rule::in(['disponible', 'en_route', 'en_maintenance', 'hors_service'])],
            'latitude'                         => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'                        => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'notes'                            => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'immatriculation.unique'  => 'Cette immatriculation est déjà enregistrée.',
            'numero_chassis.unique'   => 'Ce numéro de châssis est déjà enregistré.',
            'latitude.between'        => 'La latitude doit être entre -90 et 90.',
            'longitude.between'       => 'La longitude doit être entre -180 et 180.',
            'annee_fabrication.max'   => 'L\'année ne peut pas être dans le futur.',
        ];
    }
}