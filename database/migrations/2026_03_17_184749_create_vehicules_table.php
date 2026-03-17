<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table vehicles : tous les véhicules de la flotte
     * Ex: bus, minibus, voitures utilisés par Trésor Voyages, Buga, etc.
     */
    public function up(): void
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('marque', 100);                                       // Ex: Toyota, Mercedes, Yutong
            $table->string('modele', 100);                                       // Ex: Coaster, Sprinter, ZK6122
            $table->year('annee_fabrication');                                   // Année de fabrication
            $table->string('immatriculation', 20)->unique();                     // Plaque d'immatriculation unique
            $table->enum('type_vehicule', ['bus', 'minibus', 'berline', 'van']) // Type de véhicule
                  ->default('minibus');
            $table->integer('capacite_passagers');                               // Nombre de places passagers
            $table->string('couleur', 50)->nullable();                           // Couleur du véhicule
            $table->string('numero_chassis', 100)->unique()->nullable();         // Numéro de châssis (VIN)
            $table->enum('carburant', ['essence', 'diesel', 'hybride', 'electrique'])
                  ->default('diesel');                                           // Type de carburant
            $table->integer('kilometrage_actuel')->default(0);                   // Kilométrage actuel en km
            $table->date('date_mise_en_service')->nullable();                    // Date 1ère mise en circulation
            $table->date('date_expiration_assurance')->nullable();               // Expiration assurance
            $table->date('date_expiration_visite_technique')->nullable();        // Expiration visite technique
            $table->enum('statut', [
                'disponible',                                                    // Prêt à être affecté
                'en_route',                                                      // En mission actuellement
                'en_maintenance',                                                // En réparation/entretien
                'hors_service'                                                   // Inutilisable
            ])->default('disponible');
            $table->decimal('latitude', 10, 8)->nullable();                      // Dernière position GPS - latitude
            $table->decimal('longitude', 11, 8)->nullable();                     // Dernière position GPS - longitude
            $table->timestamp('derniere_position_at')->nullable();               // Timestamp dernière position connue
            $table->text('notes')->nullable();                                   // Notes diverses sur le véhicule
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicules');
    }
};