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
        Schema::create('fiscal_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');

            // Event details
            $table->string('country_code', 2)->comment('Código ISO del país (PY, ES, AR, etc.)');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('event_type', [
                'vat_liquidation',        // Liquidación de IVA
                'income_tax',             // Impuesto a la renta/ganancias
                'tax_declaration',        // Declaración de impuestos
                'social_security',        // Seguridad social
                'annual_accounts',        // Cuentas anuales
                'quarterly_declaration',  // Declaración trimestral
                'monthly_declaration',    // Declaración mensual
                'custom'                  // Evento personalizado
            ])->default('custom');

            // Scheduling
            $table->date('event_date');
            $table->integer('notification_days_before')->default(7)->comment('Días antes del evento para notificar');
            $table->boolean('is_recurring')->default(false)->comment('Si se repite cada año');

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false)->comment('Si es evento por defecto del país');
            $table->timestamp('last_notified_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'event_date']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['country_code', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_events');
    }
};
