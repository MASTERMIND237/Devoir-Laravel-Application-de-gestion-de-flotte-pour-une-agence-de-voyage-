<?php

namespace Database\Seeders;


use App\Models\Vehicule;
use Illuminate\Database\Seeder;
 
class VehiculesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚌 Création de la flotte de véhicules...');
 
        // Flotte principale disponible
        Vehicule::factory()->count(10)->create();                        // 10 véhicules disponibles
 
        // Véhicules avec états particuliers pour tester les fonctionnalités
        Vehicule::factory()->count(2)->enMaintenance()->create();        // 2 en maintenance
        Vehicule::factory()->count(2)->enRoute()->create();              // 2 en route (avec GPS)
        Vehicule::factory()->horsService()->create();                    // 1 hors service
        Vehicule::factory()->count(2)->assuranceExpirantBientot()->create(); // 2 assurances qui expirent
        Vehicule::factory()->visiteTechniqueExpiree()->create();         // 1 visite technique expirée
        Vehicule::factory()->count(3)->grandBus()->create();             // 3 grands bus
 
        $this->command->info('   → ' . Vehicule::count() . ' véhicules créés');
    }
}
 