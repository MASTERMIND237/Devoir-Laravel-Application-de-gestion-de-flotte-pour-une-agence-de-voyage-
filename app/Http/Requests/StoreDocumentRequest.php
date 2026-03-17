<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;                                                             // Tout user connecté peut uploader
    }

    public function rules(): array
    {
        return [
            'documentable_type' => ['required', Rule::in([
                'App\\Models\\User',
                'App\\Models\\Driver',
                'App\\Models\\Vehicule',
                'App\\Models\\Maintenance',
            ])],
            'documentable_id'   => ['required', 'integer'],
            'nom'               => ['required', 'string', 'max:200'],
            'type'              => ['required', Rule::in([
                'cni', 'passeport', 'permis_conduire', 'carte_grise',
                'assurance', 'visite_technique', 'contrat', 'facture',
                'certificat_medical', 'photo', 'autre'
            ])],
            'fichier'           => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',                             // Types autorisés
                'max:10240',                                                    // Max 10 Mo
            ],
            'description'       => ['nullable', 'string', 'max:500'],
            'date_emission'     => ['nullable', 'date', 'before_or_equal:today'],
            'date_expiration'   => ['nullable', 'date', 'after:date_emission'],
        ];
    }

    /**
     * Prépare les données avant validation
     * Infère automatiquement la taille et le format du fichier
     */
    protected function prepareForValidation(): void
    {
        if ($this->hasFile('fichier')) {
            $fichier = $this->file('fichier');
            $this->merge([
                'format_fichier' => $fichier->getClientOriginalExtension(),
                'taille_fichier' => (int) round($fichier->getSize() / 1024),   // Convertit bytes → Ko
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'documentable_type.required'  => 'Le type d\'entité associée est obligatoire.',
            'documentable_type.in'        => 'Le type d\'entité n\'est pas valide.',
            'documentable_id.required'    => 'L\'identifiant de l\'entité est obligatoire.',
            'nom.required'                => 'Le nom du document est obligatoire.',
            'type.required'               => 'Le type de document est obligatoire.',
            'type.in'                     => 'Le type de document sélectionné n\'est pas valide.',
            'fichier.required'            => 'Le fichier est obligatoire.',
            'fichier.file'                => 'Le fichier uploadé n\'est pas valide.',
            'fichier.mimes'               => 'Le fichier doit être au format : PDF, JPG, PNG, DOC ou DOCX.',
            'fichier.max'                 => 'Le fichier ne doit pas dépasser 10 Mo.',
            'date_expiration.after'       => 'La date d\'expiration doit être après la date d\'émission.',
        ];
    }
}