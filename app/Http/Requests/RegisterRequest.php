<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Public registration — accessible sans authentification
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'         => ['required', 'string', 'max:100'],
            'prenom'      => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', 'confirmed', Password::min(8)
                                ->letters()
                                ->numbers()
                                ->uncompromised()],
            // Seuls ces rôles sont autorisés via l'inscription publique
            'role'        => ['nullable', Rule::in(['gestionnaire', 'chauffeur'])],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'photo_profil'=> ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
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
            'password.confirmed'=> 'La confirmation du mot de passe ne correspond pas.',
            'role.in'           => 'Le rôle doit être : gestionnaire ou chauffeur.',
            'photo_profil.image'=> 'Le fichier doit être une image.',
            'photo_profil.max'  => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
