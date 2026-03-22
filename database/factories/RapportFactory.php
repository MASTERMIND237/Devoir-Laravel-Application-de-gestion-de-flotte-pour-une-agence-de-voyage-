<?php 


namespace Database\Factories;
 
use App\Models\Affectation;
use App\Models\Rapport;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class RapportFactory extends Factory
{
    protected $model = Rapport::class;
 
    public function definition(): array
    {
        $kmDepart  = fake()->numberBetween(80000, 300000);
        $kmParcouru= fake()->numberBetween(150, 400);       // Distance typique Yaoundé-Douala = 240km
        $kmArrivee = $kmDepart + $kmParcouru;
        $carburant = round($kmParcouru * fake()->randomFloat(2, 0.08, 0.12), 1); // 8-12 L/100km pour un bus
        $prixLitre = 730;                                    // Prix moyen du diesel au Cameroun (FCFA)
 
        return [
            'assignment_id'                => Affectation::factory()->terminee(),
            'driver_id'                    => null,                             // Sera rempli par le seeder
            'vehicle_id'                   => null,                             // Sera rempli par le seeder
            'kilometrage_depart'           => $kmDepart,
            'kilometrage_arrivee'          => $kmArrivee,
            'carburant_consomme'           => $carburant,
            'cout_carburant'               => round($carburant * $prixLitre),
            'nombre_passagers_transportes' => fake()->numberBetween(10, 55),
            'etat_vehicule_depart'         => fake()->randomElement(['bon', 'bon', 'bon', 'moyen']),
            'etat_vehicule_arrivee'        => fake()->randomElement(['bon', 'bon', 'moyen', 'moyen']),
            'incident_signale'             => fake()->boolean(10),              // 10% de chance d'incident
            'description_incident'         => null,
            'observations'                 => fake()->optional(0.3)->sentence(),
            'statut_validation'            => 'valide',
            'valide_par'                   => null,                             // Sera rempli par le seeder
            'valide_at'                    => now()->subHours(fake()->numberBetween(1, 48)),
            'date_rapport'                 => fake()->dateTimeBetween('-30 days', 'yesterday'),
        ];
    }
 
    public function enAttente(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut_validation' => 'en_attente',
            'valide_par'        => null,
            'valide_at'         => null,
        ]);
    }
 
    public function avecIncident(): static
    {
        return $this->state(fn(array $attributes) => [
            'incident_signale'    => true,
            'description_incident'=> fake()->randomElement([
                'Crevaison sur l\'autoroute à hauteur d\'Edéa',
                'Accrochage mineur au péage de Mbankomo',
                'Panne de climatisation en cours de route',
                'Contrôle de gendarmerie avec retard de 45 minutes',
                'Passager malaise pris en charge à Edéa',
            ]),
            'etat_vehicule_arrivee' => 'mauvais',
        ]);
    }
 
    public function rejete(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut_validation' => 'rejete',
            'observations'      => 'REJETÉ — Kilométrage incohérent avec les données du véhicule.',
        ]);
    }
}
 