<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;
 
class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗺️  Création des routes...');
 
        // Routes fixes réalistes (trajets camerounais)
        $routesFixees = [
            [
                'nom'                   => 'Yaoundé — Douala Express',
                'ville_depart'          => 'Yaoundé',
                'ville_arrivee'         => 'Douala',
                'point_depart'          => 'Gare Centrale Yaoundé, Avenue Kennedy',
                'point_arrivee'         => 'Gare Akwa Douala, Boulevard de la Liberté',
                'distance_km'           => 240,
                'duree_estimee'         => '03:30:00',
                'prix_billet'           => 3500,
                'arrets_intermediaires' => ['Edéa'],
                'latitude_depart'       => 3.8480,  'longitude_depart'  => 11.5021,
                'latitude_arrivee'      => 4.0511,  'longitude_arrivee' => 9.7679,
                'statut'                => 'active',
            ],
            [
                'nom'                   => 'Douala — Yaoundé Express',
                'ville_depart'          => 'Douala',
                'ville_arrivee'         => 'Yaoundé',
                'point_depart'          => 'Gare Akwa Douala, Boulevard de la Liberté',
                'point_arrivee'         => 'Gare Centrale Yaoundé, Avenue Kennedy',
                'distance_km'           => 240,
                'duree_estimee'         => '03:30:00',
                'prix_billet'           => 3500,
                'arrets_intermediaires' => ['Edéa'],
                'latitude_depart'       => 4.0511,  'longitude_depart'  => 9.7679,
                'latitude_arrivee'      => 3.8480,  'longitude_arrivee' => 11.5021,
                'statut'                => 'active',
            ],
            [
                'nom'                   => 'Yaoundé — Bafoussam',
                'ville_depart'          => 'Yaoundé',
                'ville_arrivee'         => 'Bafoussam',
                'point_depart'          => 'Gare Centrale Yaoundé',
                'point_arrivee'         => 'Gare de Bafoussam',
                'distance_km'           => 327,
                'duree_estimee'         => '04:30:00',
                'prix_billet'           => 4500,
                'arrets_intermediaires' => ['Tiko', 'Bafang'],
                'latitude_depart'       => 3.8480,  'longitude_depart'  => 11.5021,
                'latitude_arrivee'      => 5.4764,  'longitude_arrivee' => 10.4176,
                'statut'                => 'active',
            ],
            [
                'nom'                   => 'Douala — Limbé Direct',
                'ville_depart'          => 'Douala',
                'ville_arrivee'         => 'Limbé',
                'point_depart'          => 'Gare Akwa Douala',
                'point_arrivee'         => 'Gare de Limbé',
                'distance_km'           => 74,
                'duree_estimee'         => '01:30:00',
                'prix_billet'           => 1500,
                'arrets_intermediaires' => [],
                'latitude_depart'       => 4.0511,  'longitude_depart'  => 9.7679,
                'latitude_arrivee'      => 4.0173,  'longitude_arrivee' => 9.2040,
                'statut'                => 'active',
            ],
            [
                'nom'                   => 'Yaoundé — Bertoua',
                'ville_depart'          => 'Yaoundé',
                'ville_arrivee'         => 'Bertoua',
                'point_depart'          => 'Gare Centrale Yaoundé',
                'point_arrivee'         => 'Gare de Bertoua',
                'distance_km'           => 355,
                'duree_estimee'         => '05:00:00',
                'prix_billet'           => 5000,
                'arrets_intermediaires' => ['Ayos', 'Abong-Mbang'],
                'latitude_depart'       => 3.8480,  'longitude_depart'  => 11.5021,
                'latitude_arrivee'      => 4.5786,  'longitude_arrivee' => 13.6861,
                'statut'                => 'active',
            ],
            [
                'nom'                   => 'Douala — Bamenda',
                'ville_depart'          => 'Douala',
                'ville_arrivee'         => 'Bamenda',
                'point_depart'          => 'Gare Akwa Douala',
                'point_arrivee'         => 'Gare de Bamenda',
                'distance_km'           => 380,
                'duree_estimee'         => '05:30:00',
                'prix_billet'           => 5500,
                'arrets_intermediaires' => ['Bafoussam'],
                'latitude_depart'       => 4.0511,  'longitude_depart'  => 9.7679,
                'latitude_arrivee'      => 5.9631,  'longitude_arrivee' => 10.1591,
                'statut'                => 'active',
            ],
            // Route suspendue pour tester les filtres
            [
                'nom'                   => 'Yaoundé — Kribi (suspendue)',
                'ville_depart'          => 'Yaoundé',
                'ville_arrivee'         => 'Kribi',
                'point_depart'          => 'Gare Centrale Yaoundé',
                'point_arrivee'         => 'Gare de Kribi',
                'distance_km'           => 165,
                'duree_estimee'         => '02:30:00',
                'prix_billet'           => 2500,
                'arrets_intermediaires' => ['Lolodorf'],
                'latitude_depart'       => 3.8480,  'longitude_depart'  => 11.5021,
                'latitude_arrivee'      => 2.9395,  'longitude_arrivee' => 9.9071,
                'statut'                => 'suspendue',
            ],
        ];
 
        foreach ($routesFixees as $route) {
            Route::create($route);
        }
 
        $this->command->info('   → ' . Route::count() . ' routes créées');
    }
}
 