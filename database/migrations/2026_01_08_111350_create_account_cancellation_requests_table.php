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
        Schema::create('account_cancellation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            // Motivos de cancelación (múltiple selección)
            $table->json('reasons')->nullable(); // Array de razones seleccionadas
            $table->text('other_reason')->nullable(); // Si selecciona "Otro"
            $table->text('feedback')->nullable(); // Comentarios adicionales

            // Oferta de retención
            $table->string('retention_offer')->nullable(); // Qué oferta se le hizo
            $table->boolean('accepted_offer')->default(false); // Si aceptó la oferta

            // Estado
            $table->enum('status', ['pending', 'retained', 'cancelled'])->default('pending');
            $table->timestamp('cancelled_at')->nullable(); // Fecha de cancelación efectiva
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_cancellation_requests');
    }
};
