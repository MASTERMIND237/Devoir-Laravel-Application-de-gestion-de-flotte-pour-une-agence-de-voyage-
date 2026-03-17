<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table reports : rapports de kilométrage par affectation
     * Soumis par le chauffeur après chaque mission
     */
    public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affectation_id')                                   // FK vers l'affectation concernée
                  ->constrained('affectations')
                  ->onDelete('cascade');
            $table->foreignId('driver_id')                                       // FK vers le chauffeur (redondant mais utile pour requêtes directes)
                  ->constrained('drivers')
                  ->onDelete('restrict');
            $table->foreignId('vehicule_id')                                      // FK vers le véhicule
                  ->constrained('vehicules')
                  ->onDelete('restrict');
            $table->integer('kilometrage_depart');                               // Km au compteur au départ
            $table->integer('kilometrage_arrivee');                              // Km au compteur à l'arrivée
            $table->integer('kilometrage_parcouru')                              // Calculé : arrivee - depart
                  ->storedAs('kilometrage_arrivee - kilometrage_depart');        // Colonne générée automatiquement
            $table->decimal('carburant_consomme', 8, 2)->nullable();            // Litres consommés
            $table->decimal('cout_carburant', 12, 2)->nullable();               // Coût carburant en FCFA
            $table->integer('nombre_passagers_transportes')->default(0);         // Passagers réellement transportés
            $table->enum('etat_vehicule_depart', ['bon', 'moyen', 'mauvais'])   // État du véhicule au départ
                  ->default('bon');
            $table->enum('etat_vehicule_arrivee', ['bon', 'moyen', 'mauvais'])  // État du véhicule à l'arrivée
                  ->default('bon');
            $table->boolean('incident_signale')->default(false);                 // Y a-t-il eu un incident ?
            $table->text('description_incident')->nullable();                    // Si oui, description
            $table->text('observations')->nullable();                            // Observations générales du chauffeur
            $table->enum('statut_validation', [
                'en_attente',                                                    // Soumis, pas encore validé
                'valide',                                                        // Validé par le gestionnaire
                'rejete'                                                         // Rejeté (données incorrectes)
            ])->default('en_attente');
            $table->foreignId('valide_par')->nullable()                          // Gestionnaire qui a validé
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('valide_at')->nullable();                          // Date/heure de validation
            $table->date('date_rapport');                                        // Date du rapport
            $table->timestamps();
            $table->softDeletes();

            // Index pour les requêtes fréquentes
            $table->index(['driver_id', 'date_rapport']);
            $table->index(['vehicule_id', 'date_rapport']);
            $table->index('statut_validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};