<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nom de la table en base de données
     */
    protected $table = 'documents';

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'uploaded_by',
        'nom',
        'type',
        'chemin_fichier',
        'format_fichier',
        'taille_fichier',
        'description',
        'date_emission',
        'date_expiration',
        'est_expire',
    ];

    protected $casts = [
        'date_emission'   => 'date',
        'date_expiration' => 'date',
        'est_expire'      => 'boolean',
        'taille_fichier'  => 'integer',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    /**
     * Relation polymorphique inverse
     * Un document peut appartenir à User, Driver, Vehicule ou Maintenance
     *
     * Utilisation : $document->documentable (retourne le modèle parent)
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * Le user qui a uploadé ce document
     * Relation : Document -> belongsTo -> User
     */
    public function uploadeur()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // =========================================================
    // ACCESSEURS
    // =========================================================

    /**
     * URL publique du fichier
     * Utilisation : $document->url
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }

    /**
     * Taille formatée en Ko/Mo
     * Utilisation : $document->taille_formatee
     * Résultat : "245 Ko" ou "2.3 Mo"
     */
    public function getTailleFormateeAttribute(): string
    {
        if (!$this->taille_fichier) return 'N/A';

        if ($this->taille_fichier >= 1024) {
            return round($this->taille_fichier / 1024, 1) . ' Mo';
        }
        return $this->taille_fichier . ' Ko';
    }

    /**
     * Vérifie si le document est expiré (en temps réel)
     * Utilisation : $document->est_expire_maintenant
     */
    public function getEstExpireMaintenant(): bool
    {
        return $this->date_expiration && $this->date_expiration < now();
    }

    /**
     * Nombre de jours avant expiration
     * Utilisation : $document->jours_avant_expiration
     */
    public function getJoursAvantExpirationAttribute(): ?int
    {
        if ($this->date_expiration) {
            return (int) now()->diffInDays($this->date_expiration, false);
        }
        return null;
    }

    // =========================================================
    // SCOPES
    // =========================================================

    /**
     * Filtrer les documents d'un type spécifique
     * Utilisation : Document::deType('assurance')->get()
     */
    public function scopeDeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filtrer les documents expirés
     * Utilisation : Document::expires()->get()
     */
    public function scopeExpires($query)
    {
        return $query->where('date_expiration', '<', now());
    }

    /**
     * Filtrer les documents expirant bientôt
     * Utilisation : Document::expirantBientot(30)->get()
     */
    public function scopeExpirantBientot($query, int $jours = 30)
    {
        return $query->whereBetween('date_expiration', [
            now(),
            now()->addDays($jours)
        ]);
    }

    /**
     * Filtrer les photos uniquement
     * Utilisation : Document::photos()->get()
     */
    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    // =========================================================
    // BOOT — Actions automatiques
    // =========================================================

    /**
     * Met à jour automatiquement le champ est_expire avant sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($document) {
            if ($document->date_expiration) {
                $document->est_expire = $document->date_expiration < now();
            }
        });

        // Supprime le fichier physique quand le document est définitivement supprimé
        static::forceDeleted(function ($document) {
            Storage::delete($document->chemin_fichier);
        });
    }
}