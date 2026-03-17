<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table assignments : affectations (chauffeur + véhicule + route)
     * C'est le coeur de la planification — une "mission" planifiée
     * Ex: Chauffeur Jean → Bus LT-2345-A → Route Yaoundé-Douala → Lundi 06h00
     */
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')                                       // FK vers drivers
                  ->constrained('drivers')
                  ->onDelete('restrict');                                        // Empêche suppression si affectation active
            $table->foreignId('vehicule_id')                                     // FK vers vehicles
                  ->constrained('vehicules')
                  ->onDelete('restrict');
            $table->foreignId('route_id')                                       // FK vers routes
                  ->constrained('routes')
                  ->onDelete('restrict');
            $table->foreignId('created_by')                                     // Gestionnaire qui a créé l'affectation
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->date('date_depart');                                         // Date de départ prévue
            $table->time('heure_depart');                                        // Heure de départ prévue
            $table->date('date_arrivee_prevue')->nullable();                     // Date d'arrivée prévue
            $table->time('heure_arrivee_prevue')->nullable();                    // Heure d'arrivée prévue
            $table->timestamp('depart_effectif')->nullable();                    // Départ réel (rempli par chauffeur)
            $table->timestamp('arrivee_effective')->nullable();                  // Arrivée réelle
            $table->integer('nombre_passagers')->default(0);                     // Passagers embarqués
            $table->enum('statut', [
                'planifiee',                                                     // Créée mais pas encore démarrée
                'en_cours',                                                      // Véhicule en route
                'terminee',                                                      // Mission accomplie
                'annulee',                                                       // Annulée
                'incident'                                                       // Problème signalé en route
            ])->default('planifiee');
            $table->text('observations')->nullable();                            // Remarques du chauffeur ou gestionnaire
            $table->timestamps();
            $table->softDeletes();

            // Index pour accélérer les recherches fréquentes
            $table->index(['date_depart', 'statut']);
            $table->index(['driver_id', 'date_depart']);
            $table->index(['vehicule_id', 'date_depart']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};