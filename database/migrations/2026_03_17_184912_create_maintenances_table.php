<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table maintenance : suivi de tous les entretiens et réparations
     * Un véhicule peut avoir plusieurs maintenances dans le temps
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')                                      // FK vers le véhicule concerné
                  ->constrained('vehicules')
                  ->onDelete('cascade');
            $table->foreignId('created_by')                                      // Gestionnaire qui a enregistré
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->enum('type_maintenance', [
                'vidange',                                                       // Vidange d'huile
                'pneumatiques',                                                  // Changement de pneus
                'freins',                                                        // Système de freinage
                'revision_generale',                                             // Révision complète
                'carrosserie',                                                   // Réparation carrosserie
                'moteur',                                                        // Réparation moteur
                'transmission',                                                  // Boîte de vitesse
                'electrique',                                                    // Système électrique
                'climatisation',                                                 // Climatisation
                'autre'                                                          // Autre type
            ]);
            $table->string('titre', 200);                                        // Titre court de l'intervention
            $table->text('description')->nullable();                             // Description détaillée
            $table->date('date_maintenance');                                    // Date de l'intervention
            $table->time('heure_debut')->nullable();                             // Heure de début
            $table->time('heure_fin')->nullable();                               // Heure de fin
            $table->string('garage_prestataire', 200)->nullable();               // Nom du garage / technicien
            $table->decimal('cout', 12, 2)->default(0);                         // Coût en FCFA
            $table->integer('kilometrage_a_lintervention')->nullable();          // Km au moment de l'entretien
            $table->integer('prochain_entretien_km')->nullable();                // Km prévu pour le prochain entretien
            $table->date('prochaine_maintenance_date')->nullable();              // Date prévue du prochain entretien
            $table->enum('statut', [
                'planifiee',                                                     // Planifiée mais pas encore faite
                'en_cours',                                                      // En cours de réalisation
                'terminee',                                                      // Terminée
                'annulee'                                                        // Annulée
            ])->default('planifiee');
            $table->text('pieces_remplacees')->nullable();                       // Liste des pièces remplacées
            $table->text('notes')->nullable();                                   // Notes supplémentaires
            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches par véhicule et statut
            $table->index(['vehicule_id', 'statut']);
            $table->index(['date_maintenance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};