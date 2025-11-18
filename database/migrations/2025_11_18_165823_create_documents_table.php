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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Usuario que subió el documento
            $table->enum('type', ['invoice', 'receipt', 'bank_statement', 'tax_form', 'other']); // Tipo de documento
            $table->string('file_path'); // Ruta del archivo almacenado
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size'); // Tamaño en bytes
            $table->enum('ocr_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('ocr_data')->nullable(); // Datos extraídos por OCR/IA
            $table->decimal('amount', 15, 2)->nullable(); // Importe extraído
            $table->string('currency', 3)->nullable();
            $table->date('document_date')->nullable(); // Fecha del documento
            $table->string('issuer')->nullable(); // Emisor del documento
            $table->string('recipient')->nullable(); // Receptor del documento
            $table->boolean('validated')->default(false); // Validado por el usuario
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'entity_id', 'type']);
            $table->index(['entity_id', 'document_date']);
            $table->index('ocr_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
