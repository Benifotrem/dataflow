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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, encrypted
            $table->string('group')->default('general'); // general, blog, api, payment, etc.
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });

        // Insertar configuraciones por defecto
        DB::table('settings')->insert([
            [
                'key' => 'openrouter_api_key',
                'value' => null,
                'type' => 'encrypted',
                'group' => 'blog',
                'description' => 'API key de OpenRouter para generación de contenido',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pexels_api_key',
                'value' => null,
                'type' => 'encrypted',
                'group' => 'blog',
                'description' => 'API key de Pexels para imágenes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'blog_generation_model',
                'value' => 'deepseek/deepseek-chat',
                'type' => 'string',
                'group' => 'blog',
                'description' => 'Modelo de IA a usar para generación de artículos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'blog_min_words',
                'value' => '1200',
                'type' => 'string',
                'group' => 'blog',
                'description' => 'Mínimo de palabras por artículo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'blog_max_words',
                'value' => '1800',
                'type' => 'string',
                'group' => 'blog',
                'description' => 'Máximo de palabras por artículo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'blog_target_countries',
                'value' => json_encode(['es', 'mx', 'ar', 'co', 'cl', 'pe', 'ec', 've', 'uy', 'py', 'bo']),
                'type' => 'json',
                'group' => 'blog',
                'description' => 'Países objetivo para temas fiscales (ISO 3166-1 alpha-2)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
