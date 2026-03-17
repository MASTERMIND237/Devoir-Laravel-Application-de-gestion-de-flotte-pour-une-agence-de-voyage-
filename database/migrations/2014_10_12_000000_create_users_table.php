<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                                        // PK auto-increment (bigint)
            $table->string('nom', 100);                                          // Nom de famille
            $table->string('prenom', 100);                                       // Prénom
            $table->string('email')->unique();                                   // Email unique pour login
            $table->string('password');                                          // Mot de passe hashé (bcrypt)
            $table->enum('role', ['admin', 'gestionnaire', 'chauffeur'])         // Rôle dans le système
                  ->default('chauffeur');
            $table->string('telephone', 20)->nullable();                         // Ex: +237 6XX XXX XXX
            $table->string('photo_profil')->nullable();                          // Chemin vers la photo
            $table->boolean('is_active')->default(true);                         // Compte actif ou suspendu
            $table->timestamp('email_verified_at')->nullable();                  // Pour vérification email
            $table->rememberToken();                                             // Token "se souvenir de moi"
            $table->timestamps();                                                // created_at & updated_at
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
