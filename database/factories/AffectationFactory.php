<?php


namespace Database\Factories;
 
use App\Models\Affectation;
use App\Models\Driver;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class AffectationFactory extends Factory
{
    protected $model = Affectation::class;
 
    // Heures de départ réalistes pour les agences camerounaises
    private array $heuresDepart = [
        '05:30', '06:00', '06:30', '07:00',
        '08:00', '09:00', '10:00', '12:00',
        '14:00', '15:00', '16:00', '18:00',
    ];
 
    public function definition(): array
    {
        $dateDepart = fake()->dateTimeBetween('-30 days', '+15 days');
        $heureDepart = fake()->randomElement($this->heuresDepart);
 
        return [
            'driver_id'            => Driver::factory(),
            'vehicle_id'           => Vehicule::factory(),
            'route_id'             => Route::factory(),
            'created_by'           => User::factory()->gestionnaire(),
            'date_depart'          => $dateDepart,
            'heure_depart'         => $heureDepart,
            'date_arrivee_prevue'  => $dateDepart,
            'heure_arrivee_prevue' => null,
            'depart_effectif'      => null,
            'arrivee_effective'    => null,
            'nombre_passagers'     => fake()->numberBetween(5, 50),
            'statut'               => 'planifiee',
            'observations'         => null,
        ];
    }
 
    // =========================================================
    // ÉTATS
    // =========================================================
 
    public function enCours(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut'          => 'en_cours',
            'date_depart'     => today(),
            'depart_effectif' => now()->subHours(fake()->numberBetween(1, 3)),
        ]);
    }
 
    public function terminee(): static
    {
        $depart  = fake()->dateTimeBetween('-30 days', '-1 day');
        $arrivee = (clone $depart)->modify('+' . fake()->numberBetween(2, 6) . ' hours');
 
        return $this->state(fn(array $attributes) => [
            'statut'            => 'terminee',
            'date_depart'       => $depart,
            'depart_effectif'   => $depart,
            'arrivee_effective' => $arrivee,
        ]);
    }
 
    public function annulee(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut'       => 'annulee',
            'observations' => fake()->randomElement([
                'Panne mécanique du véhicule',
                'Absence du chauffeur',
                'Conditions météo défavorables',
                'Annulée à la demande du client',
            ]),
        ]);
    }
 
    public function aujourdhui(): static
    {
        return $this->state(fn(array $attributes) => [
            'date_depart' => today(),
            'statut'      => 'planifiee',
        ]);
    }
}
 