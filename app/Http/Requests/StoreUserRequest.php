<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Seuls les admins et gestionnaires peuvent créer des users
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:100'],
            'prenom'      => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', Password::min(8)
                                ->letters()
                                ->numbers()
                                ->uncompromised()],                              // Vérifie si le mot de passe est dans une base de données de leaks
            'role'        => ['required', Rule::in(['admin', 'gestionnaire', 'chauffeur'])],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'photo_profil'=> ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // Max 2Mo
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'      => 'Le nom est obligatoire.',
            'prenom.required'   => 'Le prénom est obligatoire.',
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'L\'adresse email n\'est pas valide.',
            'email.unique'      => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'role.required'     => 'Le rôle est obligatoire.',
            'role.in'           => 'Le rôle doit être : admin, gestionnaire ou chauffeur.',
            'photo_profil.image'=> 'Le fichier doit être une image.',
            'photo_profil.max'  => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}