<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affectation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'affectations';

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'route_id',
        'created_by',
        'date_depart',
        'heure_depart',
        'date_arrivee_prevue',
        'heure_arrivee_prevue',
        'depart_effectif',
        'arrivee_effective',
        'nombre_passagers',
        'statut',
        'observations',
    ];

    protected $casts = [
        'date_depart'         => 'date',
        'date_arrivee_prevue' => 'date',
        'depart_effectif'     => 'datetime',
        'arrivee_effective'   => 'datetime',
        'nombre_passagers'    => 'integer',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Une affectation appartient à un driver
     * Relation : Affectation -> belongsTo -> Driver
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Une affectation appartient à un véhicule
     * Relation : Affectation -> belongsTo -> Vehicule
     */
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    /**
     * Une affectation appartient à une route
     * Relation : Affectation -> belongsTo -> Route
     */
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    /**
     * Une affectation a été créée par un gestionnaire (User)
     * Relation : Affectation -> belongsTo -> User
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Une affectation peut avoir un rapport de kilométrage
     * Relation : Affectation -> hasOne -> Rapport
     */
    public function rapport()
    {
        return $this->hasOne(Rapport::class, 'assignment_id');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Résumé lisible de l'affectation
     * Utilisation : $affectation->resume
     * Résultat : "Jean Dupont | Toyota Coaster LT-2345 | Yaoundé → Douala | 06/03/2025"
     */
    public function getResumeAttribute(): string
    {
        $driver  = $this->driver?->nom_complet ?? 'N/A';
        $vehicule = $this->vehicule?->libelle   ?? 'N/A';
        $trajet   = $this->route?->trajet       ?? 'N/A';
        $date     = $this->date_depart?->format('d/m/Y') ?? 'N/A';

        return "{$driver} | {$vehicule} | {$trajet} | {$date}";
    }

    /**
     * Durée réelle de la mission (si terminée)
     * Utilisation : $affectation->duree_reelle_minutes
     */
    public function getDureeReelleMinutesAttribute(): ?int
    {
        if ($this->depart_effectif && $this->arrivee_effective) {
            return $this->depart_effectif->diffInMinutes($this->arrivee_effective);
        }
        return null;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer les affectations d'aujourd'hui
     * Utilisation : Affectation::aujourdhui()->get()
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date_depart', today());
    }

    /**
     * Filtrer les affectations en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    /**
     * Filtrer les affectations planifiées
     */
    public function scopePlanifiees($query)
    {
        return $query->where('statut', 'planifiee');
    }

    /**
     * Filtrer les affectations d'un driver spécifique
     * Utilisation : Affectation::pourDriver(3)->get()
     */
    public function scopePourDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Filtrer les affectations d'un véhicule spécifique
     */
    public function scopePourVehicule($query, int $vehiculeId)
    {
        return $query->where('vehicle_id', $vehiculeId);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Démarre la mission (le chauffeur appuie sur "Démarrer")
     */
    public function demarrer(): void
    {
        $this->update([
            'statut'          => 'en_cours',
            'depart_effectif' => now(),
        ]);

        // Met à jour le statut du véhicule
        $this->vehicule->update(['statut' => 'en_route']);
    }

    /**
     * Termine la mission (le chauffeur appuie sur "Arriver")
     */
    public function terminer(): void
    {
        $this->update([
            'statut'            => 'terminee',
            'arrivee_effective' => now(),
        ]);

        // Libère le véhicule
        $this->vehicule->update(['statut' => 'disponible']);
    }
}