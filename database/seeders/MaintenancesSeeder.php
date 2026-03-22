<?php

namespace Database\Seeders;

 
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Seeder;
 
class MaintenancesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Création des maintenances...');
 
        $vehicules    = Vehicule::all();
        $gestionnaire = User::where('email', 'gestionnaire@satisfy.cm')->first();
 
        $typesMaintenance = [
            ['type' => 'vidange',          'titre' => 'Vidange + filtre à huile', 'cout_min' => 15000,  'cout_max' => 30000],
            ['type' => 'pneumatiques',     'titre' => 'Remplacement 4 pneus',     'cout_min' => 80000,  'cout_max' => 200000],
            ['type' => 'freins',           'titre' => 'Révision système de freins','cout_min' => 25000, 'cout_max' => 80000],
            ['type' => 'revision_generale','titre' => 'Révision générale',         'cout_min' => 100000,'cout_max' => 350000],
            ['type' => 'electrique',       'titre' => 'Réparation système électrique', 'cout_min' => 20000, 'cout_max' => 150000],
            ['type' => 'climatisation',    'titre' => 'Entretien climatisation',   'cout_min' => 30000,  'cout_max' => 100000],
        ];
 
        $garages = [
            'Garage Central Douala',
            'Atelier Mécanique Yaoundé Centre',
            'Auto Service Bafoussam',
            'Mécanique Express Douala',
        ];
 
        // --- Maintenances terminées (historique) ---
        foreach ($vehicules as $vehicule) {
            // 2 à 4 maintenances passées par véhicule
            $nb = rand(2, 4);
            for ($i = 0; $i < $nb; $i++) {
                $type = $typesMaintenance[array_rand($typesMaintenance)];
                $date = now()->subDays(rand(30, 180))->format('Y-m-d');
 
                Maintenance::create([
                    'vehicle_id'                  => $vehicule->id,
                    'created_by'                  => $gestionnaire->id,
                    'type_maintenance'             => $type['type'],
                    'titre'                        => $type['titre'],
                    'description'                  => 'Intervention effectuée selon le planning d\'entretien.',
                    'date_maintenance'             => $date,
                    'heure_debut'                  => '08:00',
                    'heure_fin'                    => rand(10, 17) . ':00',
                    'garage_prestataire'           => $garages[array_rand($garages)],
                    'cout'                         => rand($type['cout_min'], $type['cout_max']),
                    'kilometrage_a_lintervention'  => $vehicule->kilometrage_actuel - rand(5000, 50000),
                    'prochain_entretien_km'        => $vehicule->kilometrage_actuel + rand(10000, 30000),
                    'prochaine_maintenance_date'   => now()->addDays(rand(30, 120))->format('Y-m-d'),
                    'statut'                       => 'terminee',
                    'pieces_remplacees'            => $type['type'] === 'vidange'
                        ? 'Filtre à huile, 8L huile moteur 10W40'
                        : null,
                ]);
            }
        }
 
        // --- Maintenances planifiées (à venir) ---
        $vehiculesDisponibles = Vehicule::where('statut', 'disponible')->take(4)->get();
 
        foreach ($vehiculesDisponibles as $vehicule) {
            $type = $typesMaintenance[array_rand($typesMaintenance)];
            Maintenance::create([
                'vehicle_id'        => $vehicule->id,
                'created_by'        => $gestionnaire->id,
                'type_maintenance'  => $type['type'],
                'titre'             => $type['titre'],
                'date_maintenance'  => now()->addDays(rand(3, 21))->format('Y-m-d'),
                'heure_debut'       => '08:00',
                'garage_prestataire'=> $garages[array_rand($garages)],
                'cout'              => rand($type['cout_min'], $type['cout_max']),
                'statut'            => 'planifiee',
            ]);
        }
 
        // --- 1 maintenance urgente en cours ---
        $vehiculeEnMaintenance = Vehicule::where('statut', 'en_maintenance')->first();
        if ($vehiculeEnMaintenance) {
            Maintenance::create([
                'vehicle_id'        => $vehiculeEnMaintenance->id,
                'created_by'        => $gestionnaire->id,
                'type_maintenance'  => 'moteur',
                'titre'             => '⚠️ Panne moteur — Intervention urgente',
                'description'       => 'Surchauffe moteur détectée en route. Véhicule rapatrié au garage.',
                'date_maintenance'  => today()->format('Y-m-d'),
                'heure_debut'       => '07:00',
                'garage_prestataire'=> 'Garage Central Douala',
                'cout'              => 750000,
                'statut'            => 'en_cours',
            ]);
        }
 
        $this->command->info('   → ' . Maintenance::count() . ' maintenances créées');
    }
}
 