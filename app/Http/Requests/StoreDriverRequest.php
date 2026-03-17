<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls admin et gestionnaire peuvent créer un profil driver
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'user_id'                => [
                'required',
                'exists:users,id',                                              // Le user doit exister
                Rule::unique('drivers', 'user_id'),                             // Un seul profil driver par user
            ],
            'numero_permis'          => ['required', 'string', 'max:50', 'unique:drivers,numero_permis'],
            'categorie_permis'       => ['required', Rule::in(['B', 'C', 'D', 'E'])],
            'date_delivrance_permis' => ['required', 'date', 'before:today'],   // Doit être dans le passé
            'date_expiration_permis' => ['required', 'date', 'after:date_delivrance_permis'],
            'numero_cni'             => ['nullable', 'string', 'max:50', 'unique:drivers,numero_cni'],
            'date_naissance'         => ['nullable', 'date', 'before:-18 years'], // Doit avoir au moins 18 ans
            'adresse'                => ['nullable', 'string', 'max:255'],
            'ville'                  => ['nullable', 'string', 'max:100'],
            'annees_experience'      => ['nullable', 'integer', 'min:0', 'max:50'],
            'statut'                 => ['sometimes', Rule::in(['actif', 'inactif', 'suspendu'])],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'                => 'Le user associé est obligatoire.',
            'user_id.exists'                  => 'Le user sélectionné n\'existe pas.',
            'user_id.unique'                  => 'Ce user a déjà un profil chauffeur.',
            'numero_permis.required'          => 'Le numéro de permis est obligatoire.',
            'numero_permis.unique'            => 'Ce numéro de permis est déjà enregistré.',
            'categorie_permis.required'       => 'La catégorie du permis est obligatoire.',
            'categorie_permis.in'             => 'La catégorie doit être B, C, D ou E.',
            'date_delivrance_permis.before'   => 'La date de délivrance doit être dans le passé.',
            'date_expiration_permis.after'    => 'La date d\'expiration doit être après la date de délivrance.',
            'date_naissance.before'           => 'Le chauffeur doit avoir au moins 18 ans.',
            'numero_cni.unique'               => 'Ce numéro de CNI est déjà enregistré.',
        ];
    }
}