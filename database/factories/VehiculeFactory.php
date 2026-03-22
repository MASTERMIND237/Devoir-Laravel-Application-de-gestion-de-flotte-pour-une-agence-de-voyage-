<?php

namespace Database\Factories;

use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculeFactory extends Factory
{
    protected $model = Vehicule::class;

    // Marques et modèles réels utilisés par les agences camerounaises
    private array $flotteCameroun = [
        ['marque' => 'Toyota',      'modele' => 'Coaster',     'type' => 'minibus', 'capacite' => 30],
        ['marque' => 'Toyota',      'modele' => 'HiAce',       'type' => 'minibus', 'capacite' => 15],
        ['marque' => 'Mercedes',    'modele' => 'Sprinter',    'type' => 'minibus', 'capacite' => 20],
        ['marque' => 'Yutong',      'modele' => 'ZK6122',      'type' => 'bus',     'capacite' => 55],
        ['marque' => 'Yutong',      'modele' => 'ZK6938H',     'type' => 'bus',     'capacite' => 45],
        ['marque' => 'King Long',   'modele' => 'XMQ6127',     'type' => 'bus',     'capacite' => 50],
        ['marque' => 'Higer',       'modele' => 'KLQ6128',     'type' => 'bus',     'capacite' => 49],
        ['marque' => 'Mercedes',    'modele' => 'O500',        'type' => 'bus',     'capacite' => 55],
        ['marque' => 'Volkswagen',  'modele' => 'Crafter',     'type' => 'van',     'capacite' => 18],
    ];

    // Préfixes de plaques camerounaises par région
    private array $prefixesPlaque = [
        'LT',  // Littoral (Douala)
        'CE',  // Centre (Yaoundé)
        'OU',  // Ouest (Bafoussam)
        'NW',  // Nord-Ouest (Bamenda)
        'SW',  // Sud-Ouest (Buéa)
    ];

    public function definition(): array
    {
        $vehicule = fake()->randomElement($this->flotteCameroun);
        $prefixe  = fake()->randomElement($this->prefixesPlaque);

        return [
            'marque'                           => $vehicule['marque'],
            'modele'                           => $vehicule['modele'],
            'annee_fabrication'                => fake()->numberBetween(2010, 2023),
            'immatriculation'                  => $prefixe . '-' . fake()->unique()->numerify('####') . '-' . fake()->lexify('?'),
            'type_vehicule'                    => $vehicule['type'],
            'capacite_passagers'               => $vehicule['capacite'],
            'couleur'                          => fake()->randomElement(['Blanc', 'Jaune', 'Bleu', 'Rouge', 'Argent']),
            'numero_chassis'                   => strtoupper(fake()->unique()->bothify('??#####??#####??')),
            'carburant'                        => 'diesel',
            'kilometrage_actuel'               => fake()->numberBetween(50000, 350000),
            'date_mise_en_service'             => fake()->dateTimeBetween('-8 years', '-1 year'),
            'date_expiration_assurance'        => fake()->dateTimeBetween('+1 month', '+12 months'),
            'date_expiration_visite_technique' => fake()->dateTimeBetween('+1 month', '+24 months'),
            'statut'                           => 'disponible',
            'latitude'                         => null,
            'longitude'                        => null,
            'notes'                            => null,
        ];
    }

    // =========================================================
    // ÉTATS
    // =========================================================

    public function enRoute(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut'    => 'en_route',
            // Position GPS entre Yaoundé et Douala (autoroute A1)
            'latitude'  => fake()->randomFloat(6, 3.848, 4.061),
            'longitude' => fake()->randomFloat(6, 9.702, 11.516),
            'derniere_position_at' => now()->subMinutes(fake()->numberBetween(1, 30)),
        ]);
    }

    public function enMaintenance(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'en_maintenance',
        ]);
    }

    public function horsService(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'hors_service',
        ]);
    }

    /**
     * Véhicule avec assurance expirant dans moins de 30 jours
     */
    public function assuranceExpirantBientot(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_expiration_assurance' => now()->addDays(fake()->numberBetween(1, 29)),
        ]);
    }

    /**
     * Véhicule avec visite technique expirée
     */
    public function visiteTechniqueExpiree(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_expiration_visite_technique' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    /**
     * Bus de grande capacité uniquement
     */
    public function grandBus(): static
    {
        return $this->state(fn(array $attributes) => [
            'type_vehicule'      => 'bus',
            'capacite_passagers' => fake()->numberBetween(45, 60),
            'marque'             => fake()->randomElement(['Yutong', 'King Long', 'Higer']),
        ]);
    }
}