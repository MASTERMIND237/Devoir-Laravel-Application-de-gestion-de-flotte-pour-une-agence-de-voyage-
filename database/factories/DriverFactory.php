<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    // Villes camerounaises réelles pour plus de réalisme
    private array $villesCameroun = [
        'Yaoundé', 'Douala', 'Bafoussam', 'Bamenda',
        'Garoua', 'Maroua', 'Ngaoundéré', 'Bertoua',
        'Ebolowa', 'Kribi', 'Limbé', 'Buéa',
    ];

    public function definition(): array
    {
        $dateDelivrance = fake()->dateTimeBetween('-10 years', '-1 year');
        $dateExpiration = fake()->dateTimeBetween('+1 month', '+5 years');

        return [
            'user_id'                => User::factory()->chauffeur(),       // Crée un user chauffeur lié
            'numero_permis'          => 'CM-' . fake()->unique()->numerify('######'),
            'categorie_permis'       => fake()->randomElement(['C', 'D']), // D pour transport en commun
            'date_delivrance_permis' => $dateDelivrance,
            'date_expiration_permis' => $dateExpiration,
            'numero_cni'             => fake()->unique()->numerify('##########'),
            'date_naissance'         => fake()->dateTimeBetween('-55 years', '-20 years'),
            'adresse'                => fake()->streetAddress(),
            'ville'                  => fake()->randomElement($this->villesCameroun),
            'annees_experience'      => fake()->numberBetween(1, 20),
            'statut'                 => 'actif',
            'notes'                  => null,
        ];
    }

    // =========================================================
    // ÉTATS
    // =========================================================

    /**
     * Chauffeur avec permis expirant bientôt (dans 15 jours)
     * Utile pour tester les alertes
     */
    public function permisExpirantBientot(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_expiration_permis' => now()->addDays(fake()->numberBetween(1, 15)),
        ]);
    }

    /**
     * Chauffeur avec permis déjà expiré
     */
    public function permisExpire(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_expiration_permis' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    /**
     * Chauffeur suspendu
     */
    public function suspendu(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'suspendu',
        ]);
    }

    /**
     * Chauffeur inactif
     */
    public function inactif(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'inactif',
        ]);
    }

    /**
     * Chauffeur très expérimenté (10+ ans)
     */
    public function experimente(): static
    {
        return $this->state(fn(array $attributes) => [
            'annees_experience' => fake()->numberBetween(10, 25),
            'categorie_permis'  => 'D',
        ]);
    }
}