<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Un admin peut modifier tout le monde
        // Un gestionnaire peut modifier uniquement les chauffeurs
        // Un chauffeur peut modifier uniquement son propre profil
        $userCible = $this->route('user');                                       // User ciblé par la route /users/{user}

        if ($this->user()->isAdmin()) return true;

        if ($this->user()->isGestionnaire()) {
            return $userCible->role === 'chauffeur';
        }

        return $this->user()->id === $userCible->id;                            // Chauffeur → seulement lui-même
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;                                      // Exclure l'email de l'unicité pour lui-même

        return [
            'nom'         => ['sometimes', 'string', 'max:100'],                // 'sometimes' = validé seulement si présent
            'prenom'      => ['sometimes', 'string', 'max:100'],
            'email'       => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password'    => ['sometimes', 'nullable', Password::min(8)->letters()->numbers()],
            'role'        => ['sometimes', Rule::in(['admin', 'gestionnaire', 'chauffeur'])],
            'telephone'   => ['sometimes', 'nullable', 'string', 'max:20'],
            'photo_profil'=> ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email'   => 'L\'adresse email n\'est pas valide.',
            'email.unique'  => 'Cette adresse email est déjà utilisée.',
            'role.in'       => 'Le rôle doit être : admin, gestionnaire ou chauffeur.',
            'photo_profil.image' => 'Le fichier doit être une image.',
            'photo_profil.max'   => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}