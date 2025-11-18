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
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Usuario que subió el extracto
            $table->string('file_path'); // Ruta del archivo (PDF, Excel, CSV, Imagen)
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->date('statement_date'); // Fecha del extracto
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending');
            $table->timestamp('retention_expires_at'); // Fecha de caducidad (60 días desde fin de mes)
            $table->boolean('file_deleted')->default(false); // Indica si el archivo ya fue eliminado
            $table->timestamp('file_deleted_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'entity_id']);
            $table->index(['retention_expires_at', 'file_deleted']); // Para el proceso de limpieza automática
            $table->index('statement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};
