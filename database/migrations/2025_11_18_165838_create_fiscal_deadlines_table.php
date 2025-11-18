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
        Schema::create('fiscal_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Título del plazo (ej: "Declaración IVA Q1")
            $table->text('description')->nullable();
            $table->date('due_date'); // Fecha límite
            $table->time('due_time')->nullable(); // Hora límite (opcional)
            $table->enum('type', ['tax_return', 'payment', 'filing', 'other']); // Tipo de plazo
            $table->enum('frequency', ['once', 'monthly', 'quarterly', 'annually'])->default('once'); // Frecuencia
            $table->string('country_code', 3); // País al que aplica
            $table->enum('status', ['pending', 'completed', 'overdue'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->boolean('send_reminder')->default(true);
            $table->integer('reminder_days_before')->default(7); // Días antes para recordar
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['entity_id', 'due_date']);
            $table->index(['entity_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_deadlines');
    }
};
