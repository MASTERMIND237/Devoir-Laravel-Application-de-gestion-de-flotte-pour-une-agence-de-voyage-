<?php


namespace Database\Factories;
 
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;
 
    private array $garagesCameroun = [
        'Garage Central Douala',
        'Atelier Mécanique Yaoundé Centre',
        'Auto Service Bafoussam',
        'Mécanique Express Douala',
        'Centre Auto Yaoundé',
        'Garage du Peuple Douala',
    ];
 
    private array $typesMaintenance = [
        'vidange', 'pneumatiques', 'freins',
        'revision_generale', 'carrosserie',
        'moteur', 'electrique', 'climatisation',
    ];
 
    public function definition(): array
    {
        $type = fake()->randomElement($this->typesMaintenance);
        $date = fake()->dateTimeBetween('-6 months', '+2 months');
 
        return [
            'vehicle_id'                  => Vehicule::factory(),
            'created_by'                  => User::factory()->gestionnaire(),
            'type_maintenance'            => $type,
            'titre'                       => ucfirst($type) . ' — ' . fake()->date('M Y'),
            'description'                 => fake()->sentence(10),
            'date_maintenance'            => $date,
            'heure_debut'                 => '08:00',
            'heure_fin'                   => fake()->randomElement(['10:00', '12:00', '14:00', '16:00']),
            'garage_prestataire'          => fake()->randomElement($this->garagesCameroun),
            'cout'                        => fake()->numberBetween(15000, 500000),
            'kilometrage_a_lintervention' => fake()->numberBetween(50000, 300000),
            'prochain_entretien_km'       => fake()->numberBetween(310000, 400000),
            'prochaine_maintenance_date'  => fake()->dateTimeBetween('+1 month', '+6 months'),
            'statut'                      => 'terminee',
            'pieces_remplacees'           => fake()->randomElement([
                'Filtre à huile, huile moteur 10W40',
                '4 pneus 215/75 R17',
                'Plaquettes de frein avant et arrière',
                'Courroie de distribution',
                null,
            ]),
            'notes' => null,
        ];
    }
 
    public function planifiee(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut'           => 'planifiee',
            'date_maintenance' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'heure_fin'        => null,
        ]);
    }
 
    public function enCours(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut'           => 'en_cours',
            'date_maintenance' => today(),
        ]);
    }
 
    public function urgente(): static
    {
        return $this->state(fn(array $attributes) => [
            'type_maintenance' => 'moteur',
            'titre'            => 'Panne moteur — URGENTE',
            'cout'             => fake()->numberBetween(500000, 1500000),
            'statut'           => 'en_cours',
        ]);
    }
}
 
 