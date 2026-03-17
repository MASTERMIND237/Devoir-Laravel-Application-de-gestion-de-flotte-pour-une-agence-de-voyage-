<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
 
    /**
     * Nom de la table en base de données
     */
    protected $table = 'users';
 
    /**
     * Champs autorisés à l'assignation en masse
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'photo_profil',
        'is_active',
    ];
 
    /**
     * Champs cachés dans les réponses JSON (jamais exposés via API)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
 
    /**
     * Castings de types automatiques
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',        // Hash automatique à l'assignation
        'is_active'         => 'boolean',
    ];
 
    // =========================================================
    // RELATIONS
    // =========================================================
 
    /**
     * Un user de rôle "chauffeur" possède un profil driver
     * Relation : User -> hasOne -> Driver
     */
    public function driver()
    {
        return $this->hasOne(Driver::class, 'user_id');
    }
 
    /**
     * Un gestionnaire/admin peut créer plusieurs affectations
     * Relation : User -> hasMany -> Affectation
     */
    public function affectationsCreees()
    {
        return $this->hasMany(Affectation::class, 'created_by');
    }
 
    /**
     * Un gestionnaire peut valider plusieurs rapports
     * Relation : User -> hasMany -> Rapport
     */
    public function rapportsValides()
    {
        return $this->hasMany(Rapport::class, 'valide_par');
    }
 
    /**
     * Un user peut avoir plusieurs documents (CNI, passeport, contrat...)
     * Relation polymorphique : User -> morphMany -> Document
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
 
    /**
     * Un user peut uploader plusieurs documents
     * Relation : User -> hasMany -> Document
     */
    public function documentsUploades()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
 
    // =========================================================
    // ACCESSEURS (Getters)
    // =========================================================
 
    /**
     * Nom complet du user
     * Utilisation : $user->nom_complet
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
 
    // =========================================================
    // SCOPES (Filtres réutilisables)
    // =========================================================
 
    /**
     * Filtrer uniquement les chauffeurs
     * Utilisation : User::chauffeurs()->get()
     */
    public function scopeChauffeurs($query)
    {
        return $query->where('role', 'chauffeur');
    }
 
    /**
     * Filtrer uniquement les gestionnaires
     * Utilisation : User::gestionnaires()->get()
     */
    public function scopeGestionnaires($query)
    {
        return $query->where('role', 'gestionnaire');
    }
 
    /**
     * Filtrer uniquement les comptes actifs
     * Utilisation : User::actifs()->get()
     */
    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }
 
    // =========================================================
    // HELPERS (Méthodes utilitaires)
    // =========================================================
 
    /**
     * Vérifie si le user est admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
 
    /**
     * Vérifie si le user est gestionnaire
     */
    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }
 
    /**
     * Vérifie si le user est chauffeur
     */
    public function isChauffeur(): bool
    {
        return $this->role === 'chauffeur';
    }
















    // use HasApiTokens, HasFactory, Notifiable;

    // /**
    //  * The attributes that are mass assignable.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var array<int, string>
    //  */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'hashed',
    // ];

    // public function commande()
    // {
    //     return $this->hasMany(Commande::class);
    // }
}
