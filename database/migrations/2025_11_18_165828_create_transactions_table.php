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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('set null'); // Documento relacionado (si aplica)
            $table->enum('type', ['income', 'expense', 'transfer']); // Tipo de transacción
            $table->date('transaction_date');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->string('account_code')->nullable(); // Código de cuenta contable
            $table->string('category')->nullable(); // Categoría fiscal
            $table->decimal('tax_amount', 15, 2)->nullable(); // Importe de impuestos (IVA, etc.)
            $table->decimal('tax_rate', 5, 2)->nullable(); // Tasa de impuesto
            $table->string('tax_type')->nullable(); // Tipo de impuesto (IVA, IRPF, etc.)
            $table->string('counterparty')->nullable(); // Contraparte (cliente/proveedor)
            $table->string('payment_method')->nullable(); // Método de pago
            $table->string('reference')->nullable(); // Referencia de la transacción
            $table->boolean('reconciled')->default(false); // Conciliado con extracto bancario
            $table->timestamp('reconciled_at')->nullable();
            $table->json('metadata')->nullable(); // Metadatos adicionales
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'entity_id', 'type']);
            $table->index(['entity_id', 'transaction_date']);
            $table->index(['reconciled', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
