<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'vehicle_id'                  => ['required', 'exists:vehicules,id'],
            'type_maintenance'            => ['required', Rule::in([
                'vidange', 'pneumatiques', 'freins', 'revision_generale',
                'carrosserie', 'moteur', 'transmission', 'electrique',
                'climatisation', 'autre'
            ])],
            'titre'                       => ['required', 'string', 'max:200'],
            'description'                 => ['nullable', 'string', 'max:2000'],
            'date_maintenance'            => ['required', 'date'],
            'heure_debut'                 => ['nullable', 'date_format:H:i'],
            'heure_fin'                   => ['nullable', 'date_format:H:i', 'after:heure_debut'],
            'garage_prestataire'          => ['nullable', 'string', 'max:200'],
            'cout'                        => ['nullable', 'numeric', 'min:0'],
            'kilometrage_a_lintervention' => ['nullable', 'integer', 'min:0'],
            'prochain_entretien_km'       => ['nullable', 'integer', 'min:0',
                                              'gt:kilometrage_a_lintervention'], // Doit être > km actuel
            'prochaine_maintenance_date'  => ['nullable', 'date', 'after:date_maintenance'],
            'statut'                      => ['sometimes', Rule::in(['planifiee', 'en_cours', 'terminee', 'annulee'])],
            'pieces_remplacees'           => ['nullable', 'string', 'max:1000'],
            'notes'                       => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required'                => 'Le véhicule est obligatoire.',
            'vehicle_id.exists'                  => 'Le véhicule sélectionné n\'existe pas.',
            'type_maintenance.required'          => 'Le type de maintenance est obligatoire.',
            'type_maintenance.in'                => 'Le type de maintenance sélectionné n\'est pas valide.',
            'titre.required'                     => 'Le titre de la maintenance est obligatoire.',
            'date_maintenance.required'          => 'La date de maintenance est obligatoire.',
            'heure_fin.after'                    => 'L\'heure de fin doit être après l\'heure de début.',
            'cout.min'                           => 'Le coût ne peut pas être négatif.',
            'prochain_entretien_km.gt'           => 'Le prochain entretien (km) doit être supérieur au kilométrage actuel.',
            'prochaine_maintenance_date.after'   => 'La prochaine date de maintenance doit être après la date actuelle.',
        ];
    }
}