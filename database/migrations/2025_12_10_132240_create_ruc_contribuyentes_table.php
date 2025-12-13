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
        Schema::create('ruc_contribuyentes', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20)->unique()->index()->comment('RUC sin guión');
            $table->string('dv', 2)->nullable()->comment('Dígito verificador');
            $table->string('razon_social', 255)->index()->comment('Nombre o razón social');
            $table->string('tipo_contribuyente', 50)->nullable()->comment('Tipo de contribuyente');
            $table->string('estado', 50)->default('ACTIVO')->comment('Estado del RUC');
            $table->string('ruc_anterior', 20)->nullable()->comment('RUC anterior si fue reemplazado');
            $table->text('datos_adicionales')->nullable()->comment('JSON con datos extras');
            $table->timestamp('fecha_actualizacion_set')->nullable()->comment('Última actualización en SET');
            $table->timestamps();

            // Índices adicionales para búsqueda rápida
            $table->index('estado');
            $table->fullText('razon_social'); // Para búsqueda por nombre
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruc_contribuyentes');
    }
};
