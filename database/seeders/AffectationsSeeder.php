<?php

namespace Database\Seeders;

 
use App\Models\Affectation;
use App\Models\Driver;
use App\Models\Route;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;
 
class AffectationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📋 Création des affectations...');
 
        // Récupérer les ressources existantes
        $drivers       = Driver::where('statut', 'actif')->get();
        $vehicules     = Vehicule::where('statut', 'disponible')->get();
        $routes        = Route::where('statut', 'active')->get();
        $gestionnaire  = User::where('email', 'gestionnaire@satisfy.cm')->first();
 
        $heuresDepart = ['05:30', '06:00', '07:00', '08:00', '10:00', '14:00', '16:00'];
 
        // --- Affectations terminées (historique sur 30 jours) ---
        for ($i = 30; $i >= 1; $i--) {
            $nbAffectations = rand(2, 5);                               // 2 à 5 affectations par jour passé
 
            for ($j = 0; $j < $nbAffectations; $j++) {
                $driver   = $drivers->random();
                $vehicule = $vehicules->random();
                $route    = $routes->random();
                $date     = now()->subDays($i)->format('Y-m-d');
                $heure    = $heuresDepart[array_rand($heuresDepart)];
 
                Affectation::create([
                    'driver_id'          => $driver->id,
                    'vehicle_id'         => $vehicule->id,
                    'route_id'           => $route->id,
                    'created_by'         => $gestionnaire->id,
                    'date_depart'        => $date,
                    'heure_depart'       => $heure,
                    'date_arrivee_prevue'=> $date,
                    'depart_effectif'    => $date . ' ' . $heure . ':00',
                    'arrivee_effective'  => now()->subDays($i)->addHours(rand(3, 6)),
                    'nombre_passagers'   => rand(10, $vehicule->capacite_passagers),
                    'statut'             => 'terminee',
                ]);
            }
        }
 
        // --- Affectations d'aujourd'hui ---
        $affectationsAujourdhui = [
            ['heure' => '05:30', 'statut' => 'terminee'],
            ['heure' => '07:00', 'statut' => 'en_cours'],
            ['heure' => '09:00', 'statut' => 'planifiee'],
            ['heure' => '14:00', 'statut' => 'planifiee'],
        ];
 
        foreach ($affectationsAujourdhui as $config) {
            Affectation::create([
                'driver_id'          => $drivers->random()->id,
                'vehicle_id'         => $vehicules->random()->id,
                'route_id'           => $routes->random()->id,
                'created_by'         => $gestionnaire->id,
                'date_depart'        => today(),
                'heure_depart'       => $config['heure'],
                'date_arrivee_prevue'=> today(),
                'depart_effectif'    => $config['statut'] !== 'planifiee' ? today()->setTimeFromTimeString($config['heure']) : null,
                'arrivee_effective'  => $config['statut'] === 'terminee' ? now()->subHours(1) : null,
                'nombre_passagers'   => rand(15, 45),
                'statut'             => $config['statut'],
            ]);
        }
 
        // --- Affectations futures (7 prochains jours) ---
        for ($i = 1; $i <= 7; $i++) {
            $nbAffectations = rand(2, 4);
            for ($j = 0; $j < $nbAffectations; $j++) {
                Affectation::create([
                    'driver_id'    => $drivers->random()->id,
                    'vehicle_id'   => $vehicules->random()->id,
                    'route_id'     => $routes->random()->id,
                    'created_by'   => $gestionnaire->id,
                    'date_depart'  => now()->addDays($i)->format('Y-m-d'),
                    'heure_depart' => $heuresDepart[array_rand($heuresDepart)],
                    'statut'       => 'planifiee',
                ]);
            }
        }
 
        // --- 2 affectations annulées pour tester les filtres ---
        Affectation::create([
            'driver_id'    => $drivers->random()->id,
            'vehicle_id'   => $vehicules->random()->id,
            'route_id'     => $routes->random()->id,
            'created_by'   => $gestionnaire->id,
            'date_depart'  => now()->subDays(5)->format('Y-m-d'),
            'heure_depart' => '08:00',
            'statut'       => 'annulee',
            'observations' => 'Panne mécanique du véhicule',
        ]);
 
        $this->command->info('   → ' . Affectation::count() . ' affectations créées');
    }
}