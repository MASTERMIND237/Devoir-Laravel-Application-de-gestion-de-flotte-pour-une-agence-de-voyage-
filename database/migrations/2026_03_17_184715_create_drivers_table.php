<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table drivers : profil spécifique du chauffeur
     * Lié à users via user_id (un user de rôle chauffeur a un profil driver)
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                                         // FK vers users
                  ->constrained('users')
                  ->onDelete('cascade');                                         // Si user supprimé, driver supprimé
            $table->string('numero_permis', 50)->unique();                       // Numéro du permis de conduire
            $table->enum('categorie_permis', ['B', 'C', 'D', 'E'])              // Catégorie du permis
                  ->default('D');                                                // D = transport en commun
            $table->date('date_delivrance_permis');                              // Date de délivrance du permis
            $table->date('date_expiration_permis');                              // Date d'expiration du permis
            $table->string('numero_cni', 50)->unique()->nullable();              // Carte Nationale d'Identité
            $table->date('date_naissance')->nullable();                          // Date de naissance
            $table->string('adresse')->nullable();                               // Adresse physique
            $table->string('ville', 100)->nullable();                            // Ville (ex: Yaoundé, Douala)
            $table->integer('annees_experience')->default(0);                    // Années d'expérience
            $table->enum('statut', ['actif', 'inactif', 'suspendu'])            // Statut du chauffeur
                  ->default('actif');
            $table->text('notes')->nullable();                                   // Notes internes sur le chauffeur
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};