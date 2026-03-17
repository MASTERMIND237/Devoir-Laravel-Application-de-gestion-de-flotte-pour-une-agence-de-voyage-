<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tables système nécessaires pour :
     * - password_reset_tokens : réinitialisation de mot de passe
     * - personal_access_tokens : authentification API via Laravel Sanctum
     *   (utilisé par le frontend React et la PWA mobile des chauffeurs)
     */
    public function up(): void
    {
        // Table pour les tokens de réinitialisation de mot de passe
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table pour les tokens API Sanctum
        // Chaque connexion depuis le frontend ou la PWA génère un token ici
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');                                         // Lié à l'utilisateur (polymorphique)
            $table->string('name');                                              // Nom du token (ex: "web", "mobile")
            $table->string('token', 64)->unique();                               // Le token hashé
            $table->text('abilities')->nullable();                               // Permissions JSON (ex: ["read", "write"])
            $table->timestamp('last_used_at')->nullable();                       // Dernière utilisation
            $table->timestamp('expires_at')->nullable();                         // Expiration du token
            $table->timestamps();
        });

        // Table pour les sessions (si tu utilises la session DB)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('sessions');
    }
};