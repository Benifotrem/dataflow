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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nombre de la entidad fiscal
            $table->string('tax_id')->nullable(); // NIF/CIF/RFC/etc.
            $table->string('country_code', 3); // Código ISO del país (ES, MX, AR, etc.)
            $table->string('currency_code', 3); // Código ISO de la moneda (EUR, MXN, ARS, etc.)
            $table->json('fiscal_config')->nullable(); // Configuración fiscal específica del país (IVA, retenciones, etc.)
            $table->json('chart_of_accounts')->nullable(); // Plan contable personalizado
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
