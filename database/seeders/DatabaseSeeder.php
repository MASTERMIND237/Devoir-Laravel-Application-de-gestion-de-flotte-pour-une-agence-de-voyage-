<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/*
|--------------------------------------------------------------------------
| DatabaseSeeder — Orchestrateur principal
|--------------------------------------------------------------------------
|
| Ce seeder appelle tous les sous-seeders dans le BON ORDRE
| pour respecter les contraintes de clés étrangères.
|
| ORDRE OBLIGATOIRE :
|   1. UsersSeeder        → pas de dépendances
|   2. DriversSeeder      → dépend de Users
|   3. VehiculesSeeder    → pas de dépendances
|   4. RoutesSeeder       → pas de dépendances
|   5. AffectationsSeeder → dépend de Drivers, Vehicules, Routes, Users
|   6. MaintenancesSeeder → dépend de Vehicules, Users
|   7. RapportsSeeder     → dépend de Affectations, Drivers, Vehicules
|
| COMMANDES :
|   php artisan db:seed                    → exécute ce fichier
|   php artisan migrate:fresh --seed       → recrée tout + seed
|   php artisan db:seed --class=UsersSeeder → un seeder spécifique
*/

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Démarrage du seeding — Plateforme Satisfy');
        $this->command->newLine();

        $this->call([
            UsersSeeder::class,
            DriversSeeder::class,
            VehiculesSeeder::class,
            RoutesSeeder::class,
            AffectationsSeeder::class,
            MaintenancesSeeder::class,
            RapportsSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Seeding terminé avec succès !');
        $this->command->info('👤 Admin : admin@satisfy.cm / password123');
        $this->command->info('👤 Gestionnaire : gestionnaire@satisfy.cm / password123');
    }
}