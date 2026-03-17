<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rapport extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'rapports';

    protected $fillable = [
        'assignment_id',
        'driver_id',
        'vehicle_id',
        'kilometrage_depart',
        'kilometrage_arrivee',
        'carburant_consomme',
        'cout_carburant',
        'nombre_passagers_transportes',
        'etat_vehicule_depart',
        'etat_vehicule_arrivee',
        'incident_signale',
        'description_incident',
        'observations',
        'statut_validation',
        'valide_par',
        'valide_at',
        'date_rapport',
    ];

    protected $casts = [
        'date_rapport'                 => 'date',
        'valide_at'                    => 'datetime',
        'kilometrage_depart'           => 'integer',
        'kilometrage_arrivee'          => 'integer',
        'carburant_consomme'           => 'decimal:2',
        'cout_carburant'               => 'decimal:2',
        'nombre_passagers_transportes' => 'integer',
        'incident_signale'             => 'boolean',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Un rapport appartient à une affectation
     * Relation : Rapport -> belongsTo -> Affectation
     */
    public function affectation()
    {
        return $this->belongsTo(Affectation::class, 'assignment_id');
    }

    /**
     * Un rapport est soumis par un driver
     * Relation : Rapport -> belongsTo -> Driver
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Un rapport concerne un véhicule
     * Relation : Rapport -> belongsTo -> Vehicule
     */
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    /**
     * Un rapport est validé par un gestionnaire
     * Relation : Rapport -> belongsTo -> User
     */
    public function validateur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Kilométrage parcouru (calculé)
     * Utilisation : $rapport->kilometrage_parcouru
     * Note : déjà calculé en BD via storedAs, mais accessible ici aussi
     */
    public function getKilometrageParcoruAttribute(): int
    {
        return $this->kilometrage_arrivee - $this->kilometrage_depart;
    }

    /**
     * Consommation aux 100 km
     * Utilisation : $rapport->consommation_aux_100
     */
    public function getConsommationAux100Attribute(): ?float
    {
        $km = $this->kilometrage_arrivee - $this->kilometrage_depart;
        if ($km > 0 && $this->carburant_consomme) {
            return round(($this->carburant_consomme / $km) * 100, 2);
        }
        return null;
    }

    /**
     * Coût carburant formaté
     * Utilisation : $rapport->cout_carburant_formate
     */
    public function getCoutCarburantFormateAttribute(): string
    {
        return number_format($this->cout_carburant ?? 0, 0, ',', ' ') . ' FCFA';
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer les rapports en attente de validation
     * Utilisation : Rapport::enAttente()->get()
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut_validation', 'en_attente');
    }

    /**
     * Filtrer les rapports validés
     */
    public function scopeValides($query)
    {
        return $query->where('statut_validation', 'valide');
    }

    /**
     * Filtrer les rapports avec incident signalé
     * Utilisation : Rapport::avecIncident()->get()
     */
    public function scopeAvecIncident($query)
    {
        return $query->where('incident_signale', true);
    }

    /**
     * Filtrer par période
     * Utilisation : Rapport::periode('2025-01-01', '2025-01-31')->get()
     */
    public function scopePeriode($query, string $debut, string $fin)
    {
        return $query->whereBetween('date_rapport', [$debut, $fin]);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Valide le rapport (action du gestionnaire)
     */
    public function valider(int $validateurId): void
    {
        $this->update([
            'statut_validation' => 'valide',
            'valide_par'        => $validateurId,
            'valide_at'         => now(),
        ]);

        // Met à jour le kilométrage du véhicule
        $this->vehicule->update([
            'kilometrage_actuel' => $this->kilometrage_arrivee,
        ]);
    }

    /**
     * Rejette le rapport
     */
    public function rejeter(int $validateurId): void
    {
        $this->update([
            'statut_validation' => 'rejete',
            'valide_par'        => $validateurId,
            'valide_at'         => now(),
        ]);
    }
}