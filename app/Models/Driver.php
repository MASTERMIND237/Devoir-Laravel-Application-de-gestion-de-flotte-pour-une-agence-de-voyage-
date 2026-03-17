<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'numero_permis',
        'categorie_permis',
        'date_delivrance_permis',
        'date_expiration_permis',
        'numero_cni',
        'date_naissance',
        'adresse',
        'ville',
        'annees_experience',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_delivrance_permis' => 'date',
        'date_expiration_permis' => 'date',
        'date_naissance'         => 'date',
        'annees_experience'      => 'integer',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Un driver appartient à un user
     * Relation : Driver -> belongsTo -> User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un driver peut avoir plusieurs affectations
     * Relation : Driver -> hasMany -> Affectation
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'driver_id');
    }

    /**
     * Un driver peut avoir plusieurs rapports
     * Relation : Driver -> hasMany -> Rapport
     */
    public function rapports()
    {
        return $this->hasMany(Rapport::class, 'driver_id');
    }

    /**
     * Un driver peut avoir plusieurs documents (permis, CNI scannée...)
     * Relation polymorphique : Driver -> morphMany -> Document
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Affectation actuellement en cours pour ce driver
     * Relation : Driver -> hasOne -> Affectation (filtrée)
     */
    public function affectationEnCours()
    {
        return $this->hasOne(Affectation::class, 'driver_id')
                    ->where('statut', 'en_cours');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * Nom complet via le user associé
     * Utilisation : $driver->nom_complet
     */
    public function getNomCompletAttribute(): string
    {
        return $this->user ? $this->user->nom_complet : 'N/A';
    }

    /**
     * Vérifie si le permis est expiré
     * Utilisation : $driver->permis_expire
     */
    public function getPermisExpireAttribute(): bool
    {
        return $this->date_expiration_permis < now();
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer uniquement les drivers actifs
     * Utilisation : Driver::actifs()->get()
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Filtrer les drivers disponibles (actifs + sans affectation en cours)
     * Utilisation : Driver::disponibles()->get()
     */
    public function scopeDisponibles($query)
    {
        return $query->where('statut', 'actif')
                     ->whereDoesntHave('affectations', function ($q) {
                         $q->where('statut', 'en_cours');
                     });
    }

    /**
     * Filtrer les drivers dont le permis expire dans les 30 prochains jours
     * Utilisation : Driver::permisExpirantBientot()->get()
     */
    public function scopePermisExpirantBientot($query, int $jours = 30)
    {
        return $query->whereBetween('date_expiration_permis', [
            now(),
            now()->addDays($jours)
        ]);
    }
}