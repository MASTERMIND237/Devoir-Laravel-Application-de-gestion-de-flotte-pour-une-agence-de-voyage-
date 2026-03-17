<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table documents : pièces jointes liées à Users, Vehicles, Drivers ou Maintenances
     * Utilise une RELATION POLYMORPHIQUE via documentable_id + documentable_type
     *
     * Exemples d'utilisation :
     * - User       → CNI, passeport, contrat de travail
     * - Driver     → Permis de conduire (scan), certificat médical
     * - Vehicle    → Carte grise, assurance, visite technique, photos
     * - Maintenance→ Facture du garage, bon de commande pièces
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Relation polymorphique — clé de la flexibilité
            $table->morphs('documentable');                                      // Génère documentable_id (bigint) + documentable_type (string)

            $table->foreignId('uploaded_by')                                     // Qui a uploadé le document
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->string('nom', 200);                                          // Nom affiché du document
            $table->enum('type', [
                'cni',                                                           // Carte Nationale d'Identité
                'passeport',                                                     // Passeport
                'permis_conduire',                                               // Permis de conduire
                'carte_grise',                                                   // Carte grise véhicule
                'assurance',                                                     // Document d'assurance
                'visite_technique',                                              // Attestation visite technique
                'contrat',                                                       // Contrat de travail
                'facture',                                                       // Facture de réparation
                'certificat_medical',                                            // Certificat médical chauffeur
                'photo',                                                         // Photo du véhicule ou personne
                'autre'                                                          // Autre document
            ]);
            $table->string('chemin_fichier');                                    // Chemin de stockage (storage/app/...)
            $table->string('format_fichier', 10)->nullable();                    // Extension: pdf, jpg, png, docx
            $table->integer('taille_fichier')->nullable();                       // Taille en Ko
            $table->string('description')->nullable();                           // Description optionnelle
            $table->date('date_emission')->nullable();                           // Date d'émission du document
            $table->date('date_expiration')->nullable();                         // Date d'expiration (ex: assurance)
            $table->boolean('est_expire')                                        // Calculé automatiquement
                  ->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches polymorphiques
            $table->index(['type', 'documentable_id']);
            $table->index('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};