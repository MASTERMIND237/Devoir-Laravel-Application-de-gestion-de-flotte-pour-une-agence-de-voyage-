<?php

namespace Database\Seeders;

 
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Création des utilisateurs...');
 
        // --- Comptes fixes de référence ---
        // Ces comptes ont des emails fixes pour faciliter les tests
 
        // Super Admin
        User::factory()->admin()->create([
            'nom'      => 'ADMIN',
            'prenom'   => 'Super',
            'email'    => 'admin@satisfy.cm',
            'password' => Hash::make('password123'),
        ]);
 
        // Gestionnaire principal
        User::factory()->gestionnaire()->create([
            'nom'      => 'GESTIONNAIRE',
            'prenom'   => 'Principal',
            'email'    => 'gestionnaire@satisfy.cm',
            'password' => Hash::make('password123'),
        ]);
 
        // Gestionnaire secondaire
        User::factory()->gestionnaire()->create([
            'nom'      => 'FOUDA',
            'prenom'   => 'Marie-Claire',
            'email'    => 'marie.fouda@satisfy.cm',
            'password' => Hash::make('password123'),
        ]);
 
        // Chauffeur de test (avec compte connu)
        User::factory()->chauffeur()->create([
            'nom'      => 'MVONDO',
            'prenom'   => 'Jean-Baptiste',
            'email'    => 'chauffeur@satisfy.cm',
            'password' => Hash::make('password123'),
        ]);
 
        // --- Comptes générés aléatoirement ---
        User::factory()->count(5)->gestionnaire()->create();    // 5 gestionnaires supplémentaires
        User::factory()->count(20)->chauffeur()->create();      // 20 chauffeurs aléatoires
        User::factory()->count(2)->inactif()->create();         // 2 comptes suspendus
 
        $this->command->info('   → ' . User::count() . ' utilisateurs créés');
    }
}
 