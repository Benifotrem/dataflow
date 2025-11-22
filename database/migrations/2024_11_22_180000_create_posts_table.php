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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('image_credits')->nullable();
            $table->json('keywords')->nullable(); // Palabras clave del artículo
            $table->string('country')->nullable(); // País objetivo (es, mx, ar, co, etc.)
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('views')->default(0);
            $table->json('meta_data')->nullable(); // Datos adicionales: fuente de trends, prompt usado, etc.
            $table->timestamps();

            $table->index('slug');
            $table->index('status');
            $table->index('published_at');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
