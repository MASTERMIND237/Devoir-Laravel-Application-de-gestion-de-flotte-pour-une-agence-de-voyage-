<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'vehicules';

    protected $fillable = [
        'marque',
        'modele',
        'annee_fabrication',
        'immatriculation',
        'type_vehicule',
        'capacite_passagers',
        'couleur',
        'numero_chassis',
        'carburant',
        'kilometrage_actuel',
        'date_mise_en_service',
        'date_expiration_assurance',
        'date_expiration_visite_technique',
        'statut',
        'latitude',
        'longitude',
        'derniere_position_at',
        'notes',
    ];

    protected $casts = [
        'date_mise_en_service'              => 'date',
        'date_expiration_assurance'         => 'date',
        'date_expiration_visite_technique'  => 'date',
        'derniere_position_at'              => 'datetime',
        'capacite_passagers'                => 'integer',
        'kilometrage_actuel'                => 'integer',
        'latitude'                          => 'decimal:8',
        'longitude'                         => 'decimal:8',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Un véhicule peut avoir plusieurs affectations
     * Relation : Vehicule -> hasMany -> Affectation
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'vehicle_id');
    }

    /**
     * Un véhicule peut avoir plusieurs maintenances
     * Relation : Vehicule -> hasMany -> Maintenance
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'vehicle_id');
    }

    /**
     * Un véhicule peut avoir plusieurs rapports de kilométrage
     * Relation : Vehicule -> hasMany -> Rapport
     */
    public function rapports()
    {
        return $this->hasMany(Rapport::class, 'vehicle_id');
    }

    /**
     * Un véhicule peut avoir plusieurs documents (carte grise, assurance, photos...)
     * Relation polymorphique : Vehicule -> morphMany -> Document
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * L'affectation en cours pour ce véhicule
     */
    public function affectationEnCours()
    {
        return $this->hasOne(Affectation::class, 'vehicle_id')
                    ->where('statut', 'en_cours');
    }

    /**
     * La dernière maintenance terminée
     */
    public function derniereMaintenance()
    {
        return $this->hasOne(Maintenance::class, 'vehicle_id')
                    ->where('statut', 'terminee')
                    ->latestOfMany('date_maintenance');
    }

    /**
     * La prochaine maintenance planifiée
     */
    public function prochaineMaintenance()
    {
        return $this->hasOne(Maintenance::class, 'vehicle_id')
                    ->where('statut', 'planifiee')
                    ->where('date_maintenance', '>=', now())
                    ->oldestOfMany('date_maintenance');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Libellé complet du véhicule
     * Utilisation : $vehicule->libelle
     * Résultat : "Toyota Coaster - LT-2345-A"
     */
    public function getLibelleAttribute(): string
    {
        return "{$this->marque} {$this->modele} - {$this->immatriculation}";
    }

    /**
     * Vérifie si l'assurance est expirée
     */
    public function getAssuranceExpireAttribute(): bool
    {
        return $this->date_expiration_assurance
            && $this->date_expiration_assurance < now();
    }

    /**
     * Vérifie si la visite technique est expirée
     */
    public function getVisiteTechniqueExpireAttribute(): bool
    {
        return $this->date_expiration_visite_technique
            && $this->date_expiration_visite_technique < now();
    }

    /**
     * Retourne les coordonnées GPS sous forme de tableau
     * Utilisation : $vehicule->position
     */
    public function getPositionAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
                'updated_at' => $this->derniere_position_at,
            ];
        }
        return null;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer les véhicules disponibles
     * Utilisation : Vehicule::disponibles()->get()
     */
    public function scopeDisponibles($query)
    {
        return $query->where('statut', 'disponible');
    }

    /**
     * Filtrer les véhicules en maintenance
     */
    public function scopeEnMaintenance($query)
    {
        return $query->where('statut', 'en_maintenance');
    }

    /**
     * Filtrer les véhicules dont l'assurance expire bientôt
     * Utilisation : Vehicule::assuranceExpirantBientot()->get()
     */
    public function scopeAssuranceExpirantBientot($query, int $jours = 30)
    {
        return $query->whereBetween('date_expiration_assurance', [
            now(),
            now()->addDays($jours)
        ]);
    }

    /**
     * Filtrer les véhicules dont la visite technique expire bientôt
     */
    public function scopeVisiteTechniqueExpirantBientot($query, int $jours = 30)
    {
        return $query->whereBetween('date_expiration_visite_technique', [
            now(),
            now()->addDays($jours)
        ]);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Met à jour la position GPS du véhicule
     * Appelé par l'API de localisation en temps réel
     */
    public function updatePosition(float $lat, float $lng): void
    {
        $this->update([
            'latitude'             => $lat,
            'longitude'            => $lng,
            'derniere_position_at' => now(),
        ]);
    }
}