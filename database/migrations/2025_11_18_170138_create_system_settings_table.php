<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Clave de configuración
            $table->text('value')->nullable(); // Valor (puede ser JSON)
            $table->string('type')->default('string'); // Tipo: string, integer, boolean, json, encrypted
            $table->text('description')->nullable(); // Descripción de la configuración
            $table->string('group')->default('general'); // Grupo: general, pricing, ai, email, etc.
            $table->boolean('is_public')->default(false); // Si es visible públicamente
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });

        // Insertar configuraciones por defecto
        DB::table('system_settings')->insert([
            // Precios
            ['key' => 'plan_basic_price', 'value' => '19.99', 'type' => 'decimal', 'description' => 'Precio del plan Básico (B2C)', 'group' => 'pricing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'plan_advanced_price', 'value' => '49.99', 'type' => 'decimal', 'description' => 'Precio del plan Avanzado (B2B)', 'group' => 'pricing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'addon_500_docs_price', 'value' => '9.99', 'type' => 'decimal', 'description' => 'Precio addon 500 documentos adicionales', 'group' => 'pricing', 'created_at' => now(), 'updated_at' => now()],

            // Límites
            ['key' => 'document_limit_base', 'value' => '500', 'type' => 'integer', 'description' => 'Límite base de documentos por mes', 'group' => 'limits', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'data_retention_days', 'value' => '60', 'type' => 'integer', 'description' => 'Días de retención para extractos bancarios', 'group' => 'limits', 'created_at' => now(), 'updated_at' => now()],

            // IA
            ['key' => 'ai_provider', 'value' => 'openai', 'type' => 'string', 'description' => 'Proveedor de IA (openai, openroute)', 'group' => 'ai', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_model', 'value' => 'gpt-4o-mini', 'type' => 'string', 'description' => 'Modelo de IA a utilizar', 'group' => 'ai', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'openai_api_key', 'value' => '', 'type' => 'encrypted', 'description' => 'API Key de OpenAI', 'group' => 'ai', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'openroute_api_key', 'value' => '', 'type' => 'encrypted', 'description' => 'API Key de OpenRoute', 'group' => 'ai', 'created_at' => now(), 'updated_at' => now()],

            // Email (Brevo)
            ['key' => 'brevo_api_key', 'value' => '', 'type' => 'encrypted', 'description' => 'API Key de Brevo', 'group' => 'email', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mail_from_address', 'value' => 'noreply@dataflow.com', 'type' => 'string', 'description' => 'Dirección de correo remitente', 'group' => 'email', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
