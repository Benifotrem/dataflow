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
        Schema::table('documents', function (Blueprint $table) {
            // Invoice identification
            $table->string('invoice_number')->nullable()->after('recipient');
            $table->string('invoice_series')->nullable()->after('invoice_number');

            // VAT/Tax fields - Base imponible y desglose de IVA
            $table->decimal('tax_base', 15, 2)->nullable()->after('amount')->comment('Base imponible (sin IVA)');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('tax_base')->comment('Tipo de IVA (%)');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('tax_rate')->comment('Importe de IVA');
            $table->decimal('total_with_tax', 15, 2)->nullable()->after('tax_amount')->comment('Total con IVA incluido');

            // Validation and quality control
            $table->boolean('is_invoice')->default(false)->after('ocr_status')->comment('Si es factura válida o no');
            $table->enum('quality_status', ['good', 'poor', 'unreadable'])->nullable()->after('is_invoice')->comment('Calidad del documento');
            $table->text('rejection_reason')->nullable()->after('quality_status')->comment('Razón de rechazo si aplica');

            // Add index for VAT reports
            $table->index(['tenant_id', 'is_invoice', 'document_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'is_invoice', 'document_date']);
            $table->dropColumn([
                'invoice_number',
                'invoice_series',
                'tax_base',
                'tax_rate',
                'tax_amount',
                'total_with_tax',
                'is_invoice',
                'quality_status',
                'rejection_reason',
            ]);
        });
    }
};
