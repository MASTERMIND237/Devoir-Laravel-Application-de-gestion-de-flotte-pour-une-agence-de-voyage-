<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        $driverId = $this->route('driver')->id;

        return [
            'numero_permis'          => ['sometimes', 'string', 'max:50',
                                         Rule::unique('drivers', 'numero_permis')->ignore($driverId)],
            'categorie_permis'       => ['sometimes', Rule::in(['B', 'C', 'D', 'E'])],
            'date_delivrance_permis' => ['sometimes', 'date', 'before:today'],
            'date_expiration_permis' => ['sometimes', 'date', 'after:date_delivrance_permis'],
            'numero_cni'             => ['sometimes', 'nullable', 'string', 'max:50',
                                         Rule::unique('drivers', 'numero_cni')->ignore($driverId)],
            'date_naissance'         => ['sometimes', 'nullable', 'date', 'before:-18 years'],
            'adresse'                => ['sometimes', 'nullable', 'string', 'max:255'],
            'ville'                  => ['sometimes', 'nullable', 'string', 'max:100'],
            'annees_experience'      => ['sometimes', 'nullable', 'integer', 'min:0', 'max:50'],
            'statut'                 => ['sometimes', Rule::in(['actif', 'inactif', 'suspendu'])],
            'notes'                  => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_permis.unique'         => 'Ce numéro de permis est déjà enregistré.',
            'categorie_permis.in'          => 'La catégorie doit être B, C, D ou E.',
            'date_delivrance_permis.before'=> 'La date de délivrance doit être dans le passé.',
            'date_expiration_permis.after' => 'La date d\'expiration doit être après la date de délivrance.',
            'date_naissance.before'        => 'Le chauffeur doit avoir au moins 18 ans.',
            'numero_cni.unique'            => 'Ce numéro de CNI est déjà enregistré.',
        ];
    }
}