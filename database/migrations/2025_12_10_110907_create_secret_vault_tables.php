<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de bóveda de secretos encriptados
        Schema::create('secret_vault', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value'); // Valor encriptado
            $table->string('hash', 64); // SHA-256 hash para integridad
            $table->json('metadata')->nullable();
            $table->timestamp('last_rotated_at')->nullable();
            $table->timestamps();
        });

        // Tabla de auditoría de accesos a secretos
        Schema::create('secret_audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('secret_key')->index();
            $table->string('action', 20)->index(); // STORE, READ, DELETE, ROTATE
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('timestamp')->index();
        });

        // Índices compuestos para búsquedas rápidas
        Schema::table('secret_audit_log', function (Blueprint $table) {
            $table->index(['secret_key', 'timestamp']);
            $table->index(['ip_address', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secret_audit_log');
        Schema::dropIfExists('secret_vault');
    }
};
