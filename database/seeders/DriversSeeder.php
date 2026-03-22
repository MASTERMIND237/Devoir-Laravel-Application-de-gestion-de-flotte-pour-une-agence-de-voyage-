<?php

namespace Database\Seeders;

 
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
 
class DriversSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚗 Création des profils chauffeurs...');
 
        // Créer un profil Driver pour le chauffeur de test
        $chauffeurTest = User::where('email', 'chauffeur@satisfy.cm')->first();
        Driver::factory()->create([
            'user_id'       => $chauffeurTest->id,
            'numero_permis' => 'CM-TEST-001',
            'statut'        => 'actif',
        ]);
 
        // Récupérer tous les users chauffeurs sans profil driver
        $chauffeursSansProfil = User::where('role', 'chauffeur')
            ->whereDoesntHave('driver')
            ->get();
 
        // Créer un profil Driver pour chacun
        foreach ($chauffeursSansProfil as $user) {
            Driver::factory()->create(['user_id' => $user->id]);
        }
 
        // Quelques états particuliers pour tester les alertes
        Driver::factory()
            ->count(2)
            ->permisExpirantBientot()
            ->create([
                'user_id' => User::factory()->chauffeur()->create()->id,
            ]);
 
        Driver::factory()
            ->suspendu()
            ->create([
                'user_id' => User::factory()->chauffeur()->create()->id,
            ]);
 
        $this->command->info('   → ' . Driver::count() . ' profils chauffeurs créés');
    }
}