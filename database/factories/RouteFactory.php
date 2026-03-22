<?php


namespace Database\Factories;
 
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class RouteFactory extends Factory
{
    protected $model = Route::class;
 
    // Trajets réels des agences camerounaises
    private array $trajets = [
        [
            'depart'    => 'Yaoundé',
            'arrivee'   => 'Douala',
            'distance'  => 240,
            'duree'     => '03:30:00',
            'prix'      => 3500,
            'lat_dep'   => 3.8480,  'lng_dep' => 11.5021,
            'lat_arr'   => 4.0511,  'lng_arr' => 9.7679,
            'arrets'    => ['Edéa'],
        ],
        [
            'depart'    => 'Douala',
            'arrivee'   => 'Yaoundé',
            'distance'  => 240,
            'duree'     => '03:30:00',
            'prix'      => 3500,
            'lat_dep'   => 4.0511,  'lng_dep' => 9.7679,
            'lat_arr'   => 3.8480,  'lng_arr' => 11.5021,
            'arrets'    => ['Edéa'],
        ],
        [
            'depart'    => 'Yaoundé',
            'arrivee'   => 'Bafoussam',
            'distance'  => 327,
            'duree'     => '04:00:00',
            'prix'      => 4000,
            'lat_dep'   => 3.8480,  'lng_dep' => 11.5021,
            'lat_arr'   => 5.4764,  'lng_arr' => 10.4176,
            'arrets'    => ['Tiko', 'Bafang'],
        ],
        [
            'depart'    => 'Douala',
            'arrivee'   => 'Bafoussam',
            'distance'  => 270,
            'duree'     => '03:45:00',
            'prix'      => 3500,
            'lat_dep'   => 4.0511,  'lng_dep' => 9.7679,
            'lat_arr'   => 5.4764,  'lng_arr' => 10.4176,
            'arrets'    => ['Bafang'],
        ],
        [
            'depart'    => 'Yaoundé',
            'arrivee'   => 'Bertoua',
            'distance'  => 355,
            'duree'     => '05:00:00',
            'prix'      => 5000,
            'lat_dep'   => 3.8480,  'lng_dep' => 11.5021,
            'lat_arr'   => 4.5786,  'lng_arr' => 13.6861,
            'arrets'    => ['Ayos', 'Abong-Mbang'],
        ],
        [
            'depart'    => 'Douala',
            'arrivee'   => 'Limbé',
            'distance'  => 74,
            'duree'     => '01:30:00',
            'prix'      => 1500,
            'lat_dep'   => 4.0511,  'lng_dep' => 9.7679,
            'lat_arr'   => 4.0173,  'lng_arr' => 9.2040,
            'arrets'    => [],
        ],
        [
            'depart'    => 'Yaoundé',
            'arrivee'   => 'Ebolowa',
            'distance'  => 156,
            'duree'     => '02:30:00',
            'prix'      => 2500,
            'lat_dep'   => 3.8480,  'lng_dep' => 11.5021,
            'lat_arr'   => 2.9000,  'lng_arr' => 11.1500,
            'arrets'    => ['Ngoulemakong'],
        ],
        [
            'depart'    => 'Douala',
            'arrivee'   => 'Bamenda',
            'distance'  => 380,
            'duree'     => '05:30:00',
            'prix'      => 5500,
            'lat_dep'   => 4.0511,  'lng_dep' => 9.7679,
            'lat_arr'   => 5.9631,  'lng_arr' => 10.1591,
            'arrets'    => ['Bafoussam'],
        ],
    ];
 
    public function definition(): array
    {
        $trajet = fake()->randomElement($this->trajets);
 
        return [
            'nom'                    => $trajet['depart'] . ' — ' . $trajet['arrivee'] . ' Express',
            'ville_depart'           => $trajet['depart'],
            'ville_arrivee'          => $trajet['arrivee'],
            'point_depart'           => 'Gare routière de ' . $trajet['depart'],
            'point_arrivee'          => 'Gare routière de ' . $trajet['arrivee'],
            'distance_km'            => $trajet['distance'],
            'duree_estimee'          => $trajet['duree'],
            'prix_billet'            => $trajet['prix'],
            'arrets_intermediaires'  => $trajet['arrets'],
            'latitude_depart'        => $trajet['lat_dep'],
            'longitude_depart'       => $trajet['lng_dep'],
            'latitude_arrivee'       => $trajet['lat_arr'],
            'longitude_arrivee'      => $trajet['lng_arr'],
            'statut'                 => 'active',
            'description'            => 'Liaison directe ' . $trajet['depart'] . ' ↔ ' . $trajet['arrivee'],
        ];
    }
 
    public function suspendue(): static
    {
        return $this->state(fn(array $attributes) => ['statut' => 'suspendue']);
    }
}
 