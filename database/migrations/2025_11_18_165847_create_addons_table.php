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
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('document_pack'); // Tipo de addon (document_pack, storage, etc.)
            $table->integer('document_quantity')->default(500); // Cantidad de documentos adicionales
            $table->decimal('price', 10, 2); // Precio pagado
            $table->year('year'); // Año de aplicación
            $table->tinyInteger('month'); // Mes de aplicación (1-12)
            $table->enum('status', ['active', 'consumed', 'expired'])->default('active');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'year', 'month', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
