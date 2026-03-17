<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'routes';

    protected $fillable = [
        'nom',
        'ville_depart',
        'ville_arrivee',
        'point_depart',
        'point_arrivee',
        'distance_km',
        'duree_estimee',
        'prix_billet',
        'arrets_intermediaires',
        'latitude_depart',
        'longitude_depart',
        'latitude_arrivee',
        'longitude_arrivee',
        'statut',
        'description',
    ];

    protected $casts = [
        'distance_km'            => 'integer',
        'prix_billet'            => 'decimal:2',
        'arrets_intermediaires'  => 'array',         // JSON → tableau PHP automatiquement
        'latitude_depart'        => 'decimal:8',
        'longitude_depart'       => 'decimal:8',
        'latitude_arrivee'       => 'decimal:8',
        'longitude_arrivee'      => 'decimal:8',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Une route peut avoir plusieurs affectations
     * Relation : Route -> hasMany -> Affectation
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'route_id');
    }

    /**
     * Affectations actives (planifiées ou en cours) sur cette route
     */
    public function affectationsActives()
    {
        return $this->hasMany(Affectation::class, 'route_id')
                    ->whereIn('statut', ['planifiee', 'en_cours']);
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Libellé court de la route
     * Utilisation : $route->trajet
     * Résultat : "Yaoundé → Douala"
     */
    public function getTrajetAttribute(): string
    {
        return "{$this->ville_depart} → {$this->ville_arrivee}";
    }

    /**
     * Coordonnées GPS du départ sous forme de tableau
     */
    public function getCoordonneesDepart(): array
    {
        return [
            'lat' => $this->latitude_depart,
            'lng' => $this->longitude_depart,
        ];
    }

    /**
     * Coordonnées GPS de l'arrivée sous forme de tableau
     */
    public function getCoordonneesArrivee(): array
    {
        return [
            'lat' => $this->latitude_arrivee,
            'lng' => $this->longitude_arrivee,
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer uniquement les routes actives
     * Utilisation : Route::actives()->get()
     */
    public function scopeActives($query)
    {
        return $query->where('statut', 'active');
    }

    /**
     * Filtrer par ville de départ
     * Utilisation : Route::depuisVille('Yaoundé')->get()
     */
    public function scopeDepuisVille($query, string $ville)
    {
        return $query->where('ville_depart', $ville);
    }

    /**
     * Filtrer par ville d'arrivée
     * Utilisation : Route::versVille('Douala')->get()
     */
    public function scopeVersVille($query, string $ville)
    {
        return $query->where('ville_arrivee', $ville);
    }

    /**
     * Rechercher une route entre deux villes
     * Utilisation : Route::entre('Yaoundé', 'Douala')->first()
     */
    public function scopeEntre($query, string $depart, string $arrivee)
    {
        return $query->where('ville_depart', $depart)
                     ->where('ville_arrivee', $arrivee);
    }
}