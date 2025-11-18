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
        Schema::create('ai_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('set null');
            $table->year('year'); // Año del uso
            $table->tinyInteger('month'); // Mes del uso (1-12)
            $table->integer('documents_processed')->default(0); // Documentos procesados en el mes
            $table->integer('api_calls')->default(0); // Llamadas a la API de IA
            $table->decimal('cost', 10, 4)->default(0); // Costo estimado
            $table->string('provider'); // openai, openroute, etc.
            $table->json('metadata')->nullable(); // Metadatos adicionales
            $table->timestamps();

            $table->unique(['tenant_id', 'year', 'month']); // Un registro por tenant por mes
            $table->index(['tenant_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage');
    }
};
