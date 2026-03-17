<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table routes : itinéraires entre les villes
     * Ex: Yaoundé → Douala, Yaoundé → Bafoussam, Douala → Limbé
     */
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);                                          // Ex: "Yaoundé - Douala Express"
            $table->string('ville_depart', 100);                                 // Ex: Yaoundé
            $table->string('ville_arrivee', 100);                                // Ex: Douala
            $table->string('point_depart', 200)->nullable();                     // Adresse précise du point de départ
            $table->string('point_arrivee', 200)->nullable();                    // Adresse précise du point d'arrivée
            $table->integer('distance_km');                                      // Distance en kilomètres
            $table->time('duree_estimee');                                       // Durée estimée (HH:MM:SS)
            $table->decimal('prix_billet', 10, 2);                              // Prix du billet en FCFA
            $table->json('arrets_intermediaires')->nullable();                   // Villes étapes en JSON ["Edéa","Mbalmayo"]
            $table->decimal('latitude_depart', 10, 8)->nullable();              // Coordonnées GPS départ
            $table->decimal('longitude_depart', 11, 8)->nullable();
            $table->decimal('latitude_arrivee', 10, 8)->nullable();             // Coordonnées GPS arrivée
            $table->decimal('longitude_arrivee', 11, 8)->nullable();
            $table->enum('statut', ['active', 'suspendue', 'supprimee'])        // État de la route
                  ->default('active');
            $table->text('description')->nullable();                             // Description ou infos utiles
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};