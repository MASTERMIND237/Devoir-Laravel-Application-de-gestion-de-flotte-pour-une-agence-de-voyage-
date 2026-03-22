<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| DESIGN PATTERN : FACTORY METHOD
|--------------------------------------------------------------------------
|
| POURQUOI LE FACTORY PATTERN ICI ?
|
| Le Factory Method délègue la CRÉATION d'objets complexes à une
| classe spécialisée. Au lieu d'instancier manuellement chaque
| objet avec "new User([...données...]) " dans chaque test ou seeder,
| on dit simplement : User::factory()->make()
|
| AVANTAGES CONCRETS :
|
|  → Création rapide : User::factory()->count(10)->create()
|    génère 10 users réalistes en une ligne.
|
|  → États prédéfinis (states) : User::factory()->admin()->create()
|    crée directement un admin sans répéter la config.
|
|  → Cohérence : toutes les données générées respectent
|    les contraintes (email unique, password hashé, etc.)
|
|  → Isolation des tests : chaque test crée ses propres données
|    sans dépendre des autres.
|
| UTILISATION :
|   User::factory()->create()                    // 1 user chauffeur
|   User::factory()->admin()->create()           // 1 admin
|   User::factory()->gestionnaire()->create()    // 1 gestionnaire
|   User::factory()->inactif()->create()         // 1 user suspendu
|   User::factory()->count(5)->chauffeur()->create() // 5 chauffeurs
*/

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Définition par défaut — génère un chauffeur actif
     */
    public function definition(): array
    {
        return [
            'nom'               => fake()->lastName(),
            'prenom'            => fake()->firstName(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => Hash::make('password123'),               // Mot de passe par défaut pour les tests
            'role'              => 'chauffeur',                             // Rôle par défaut
            'telephone'         => '+237 6' . fake()->numerify('## ### ###'),
            'photo_profil'      => null,
            'is_active'         => true,
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    // =========================================================
    // ÉTATS (States) — variations du factory
    // =========================================================

    /**
     * Crée un administrateur
     * Utilisation : User::factory()->admin()->create()
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role'   => 'admin',
            'nom'    => 'ADMIN',
            'prenom' => 'Super',
            'email'  => 'admin@satisfy.cm',
        ]);
    }

    /**
     * Crée un gestionnaire
     * Utilisation : User::factory()->gestionnaire()->create()
     */
    public function gestionnaire(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'gestionnaire',
        ]);
    }

    /**
     * Crée un chauffeur explicitement
     */
    public function chauffeur(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'chauffeur',
        ]);
    }

    /**
     * Crée un compte inactif/suspendu
     */
    public function inactif(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Crée un user avec email non vérifié
     */
    public function nonVerifie(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}


















// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;

// /**
//  * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
//  */
// class UserFactory extends Factory
// {
//     /**
//      * The current password being used by the factory.
//      */
//     protected static ?string $password;

//     /**
//      * Define the model's default state.
//      *
//      * @return array<string, mixed>
//      */
//     public function definition(): array
//     {
//         return [
//             'name' => fake()->name(),
//             'email' => fake()->unique()->safeEmail(),
//             'email_verified_at' => now(),
//             'password' => static::$password ??= Hash::make('password'),
//             'remember_token' => Str::random(10),
//         ];
//     }

//     /**
//      * Indicate that the model's email address should be unverified.
//      */
//     public function unverified(): static
//     {
//         return $this->state(fn (array $attributes) => [
//             'email_verified_at' => null,
//         ]);
//     }
// }
