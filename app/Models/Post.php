<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'image_credits',
        'keywords',
        'country',
        'status',
        'published_at',
        'created_by',
        'views',
        'meta_data',
    ];

    protected $casts = [
        'keywords' => 'array',
        'meta_data' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Generar slug automáticamente
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Relación con el usuario creador
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para posts publicados
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope para posts por país
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Incrementar vistas
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Obtener URL del post
     */
    public function getUrlAttribute()
    {
        return route('blog.show', $this->slug);
    }

    /**
     * Obtener tiempo de lectura estimado (palabras / 200 palabras por minuto)
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        return $minutes;
    }
}
