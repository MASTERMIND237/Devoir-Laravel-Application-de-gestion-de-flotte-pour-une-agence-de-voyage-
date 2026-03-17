<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'maintenances';

    protected $fillable = [
        'vehicle_id',
        'created_by',
        'type_maintenance',
        'titre',
        'description',
        'date_maintenance',
        'heure_debut',
        'heure_fin',
        'garage_prestataire',
        'cout',
        'kilometrage_a_lintervention',
        'prochain_entretien_km',
        'prochaine_maintenance_date',
        'statut',
        'pieces_remplacees',
        'notes',
    ];

    protected $casts = [
        'date_maintenance'           => 'date',
        'prochaine_maintenance_date' => 'date',
        'cout'                       => 'decimal:2',
        'kilometrage_a_lintervention'=> 'integer',
        'prochain_entretien_km'      => 'integer',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Une maintenance appartient à un véhicule
     * Relation : Maintenance -> belongsTo -> Vehicule
     */
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicle_id');
    }

    /**
     * Une maintenance a été créée par un gestionnaire
     * Relation : Maintenance -> belongsTo -> User
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Une maintenance peut avoir des documents (factures, bons de commande...)
     * Relation polymorphique : Maintenance -> morphMany -> Document
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Coût formaté en FCFA
     * Utilisation : $maintenance->cout_formate
     * Résultat : "45 000 FCFA"
     */
    public function getCoutFormateAttribute(): string
    {
        return number_format($this->cout, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Durée de l'intervention en minutes
     */
    public function getDureeMinutesAttribute(): ?int
    {
        if ($this->heure_debut && $this->heure_fin) {
            $debut = \Carbon\Carbon::parse($this->heure_debut);
            $fin   = \Carbon\Carbon::parse($this->heure_fin);
            return $debut->diffInMinutes($fin);
        }
        return null;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer les maintenances planifiées
     * Utilisation : Maintenance::planifiees()->get()
     */
    public function scopePlanifiees($query)
    {
        return $query->where('statut', 'planifiee');
    }

    /**
     * Filtrer les maintenances terminées
     */
    public function scopeTerminees($query)
    {
        return $query->where('statut', 'terminee');
    }

    /**
     * Filtrer les maintenances d'un type spécifique
     * Utilisation : Maintenance::deType('vidange')->get()
     */
    public function scopeDeType($query, string $type)
    {
        return $query->where('type_maintenance', $type);
    }

    /**
     * Filtrer les maintenances planifiées dans les X prochains jours
     * Utilisation : Maintenance::prochainesDans(7)->get()
     */
    public function scopeProchainesDans($query, int $jours = 7)
    {
        return $query->where('statut', 'planifiee')
                     ->whereBetween('date_maintenance', [
                         now(),
                         now()->addDays($jours)
                     ]);
    }

    /**
     * Coût total des maintenances (pour agrégation)
     * Utilisation : Maintenance::where('vehicle_id', 1)->coutTotal()
     */
    public function scopeCoutTotal($query)
    {
        return $query->sum('cout');
    }
}