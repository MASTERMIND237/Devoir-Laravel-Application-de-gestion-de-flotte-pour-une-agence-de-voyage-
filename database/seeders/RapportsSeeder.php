<?php

namespace Database\Seeders;
 
use App\Models\Affectation;
use App\Models\Rapport;
use App\Models\User;
use Illuminate\Database\Seeder;
 
class RapportsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📊 Création des rapports de kilométrage...');
 
        $gestionnaire = User::where('email', 'gestionnaire@satisfy.cm')->first();
 
        // Récupérer toutes les affectations terminées
        $affectationsTerminees = Affectation::where('statut', 'terminee')
            ->with('vehicule')
            ->get();
 
        foreach ($affectationsTerminees as $affectation) {
            // 85% des affectations terminées ont un rapport (réaliste)
            if (rand(1, 100) > 85) continue;
 
            $kmDepart   = $affectation->vehicule->kilometrage_actuel - rand(100, 5000);
            $kmParcouru = rand(150, 400);                                // km typique d'un trajet
            $kmArrivee  = $kmDepart + $kmParcouru;
            $carburant  = round($kmParcouru * rand(8, 12) / 100, 1);    // 8-12 L/100km
            $avecIncident = rand(1, 100) <= 8;                          // 8% de chance d'incident
 
            $rapport = Rapport::create([
                'assignment_id'                => $affectation->id,
                'driver_id'                    => $affectation->driver_id,
                'vehicle_id'                   => $affectation->vehicle_id,
                'kilometrage_depart'           => $kmDepart,
                'kilometrage_arrivee'          => $kmArrivee,
                'carburant_consomme'           => $carburant,
                'cout_carburant'               => round($carburant * 730),  // 730 FCFA/L diesel au Cameroun
                'nombre_passagers_transportes' => rand(10, $affectation->vehicule->capacite_passagers),
                'etat_vehicule_depart'         => 'bon',
                'etat_vehicule_arrivee'        => $avecIncident ? 'mauvais' : fake()->randomElement(['bon', 'bon', 'moyen']),
                'incident_signale'             => $avecIncident,
                'description_incident'         => $avecIncident
                    ? fake()->randomElement([
                        'Crevaison sur l\'autoroute à hauteur d\'Edéa',
                        'Contrôle de gendarmerie avec retard de 45 minutes',
                        'Accrochage mineur au péage de Mbankomo',
                        'Passager malaise pris en charge à Edéa',
                    ])
                    : null,
                'observations'                 => rand(1, 10) > 7 ? 'RAS. Trajet sans incident notable.' : null,
                'statut_validation'            => fake()->randomElement(['valide', 'valide', 'valide', 'en_attente']), // 75% validés
                'valide_par'                   => $gestionnaire->id,
                'valide_at'                    => now()->subHours(rand(1, 72)),
                'date_rapport'                 => $affectation->arrivee_effective
                    ? $affectation->arrivee_effective->format('Y-m-d')
                    : $affectation->date_depart->format('Y-m-d'),
            ]);
        }
 
        // --- Quelques rapports en attente de validation ---
        $affectationsSansRapport = Affectation::where('statut', 'terminee')
            ->whereDoesntHave('rapport')
            ->take(5)
            ->with('vehicule')
            ->get();
 
        foreach ($affectationsSansRapport as $affectation) {
            $kmDepart  = $affectation->vehicule->kilometrage_actuel - rand(100, 1000);
            $kmArrivee = $kmDepart + rand(150, 400);
 
            Rapport::create([
                'assignment_id'                => $affectation->id,
                'driver_id'                    => $affectation->driver_id,
                'vehicle_id'                   => $affectation->vehicle_id,
                'kilometrage_depart'           => $kmDepart,
                'kilometrage_arrivee'          => $kmArrivee,
                'carburant_consomme'           => round(($kmArrivee - $kmDepart) * 0.10, 1),
                'cout_carburant'               => round(($kmArrivee - $kmDepart) * 0.10 * 730),
                'nombre_passagers_transportes' => rand(10, 40),
                'etat_vehicule_depart'         => 'bon',
                'etat_vehicule_arrivee'        => 'bon',
                'incident_signale'             => false,
                'statut_validation'            => 'en_attente',              // En attente de validation
                'valide_par'                   => null,
                'valide_at'                    => null,
                'date_rapport'                 => now()->subDays(rand(1, 3))->format('Y-m-d'),
            ]);
        }
 
        $this->command->info('   → ' . Rapport::count() . ' rapports créés');
    }
}
 