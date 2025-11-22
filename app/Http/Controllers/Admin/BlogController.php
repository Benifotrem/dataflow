<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\BlogGeneratorService;
use App\Services\TrendingTopicsService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $blogGenerator;
    protected $trendsService;

    public function __construct(BlogGeneratorService $blogGenerator, TrendingTopicsService $trendsService)
    {
        $this->blogGenerator = $blogGenerator;
        $this->trendsService = $trendsService;
    }

    /**
     * Verificar permisos de admin
     */
    protected function checkAdminPermission()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }
    }

    /**
     * Lista de artículos
     */
    public function index(Request $request)
    {
        $this->checkAdminPermission();

        $query = Post::query()->with('creator');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);
        $countries = $this->trendsService->getAvailableCountries();

        return view('admin.blog.index', compact('posts', 'countries'));
    }

    /**
     * Formulario de generación
     */
    public function create()
    {
        $this->checkAdminPermission();

        $countries = $this->trendsService->getAvailableCountries();
        $countryNames = $this->getCountryNames();

        return view('admin.blog.create', compact('countries', 'countryNames'));
    }

    /**
     * Generar artículo con IA
     */
    public function generate(Request $request)
    {
        $this->checkAdminPermission();

        $request->validate([
            'country' => 'nullable|string|size:2',
            'topic' => 'nullable|string|max:255',
        ]);

        try {
            $post = $this->blogGenerator->generateArticle(
                $request->country,
                $request->topic,
                auth()->id()
            );

            return redirect()
                ->route('admin.blog.edit', $post->id)
                ->with('success', '¡Artículo generado exitosamente! Puedes revisarlo y editarlo antes de publicar.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al generar el artículo: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Ver/Editar artículo
     */
    public function edit(Post $post)
    {
        $countries = $this->trendsService->getAvailableCountries();
        $countryNames = $this->getCountryNames();

        return view('admin.blog.edit', compact('post', 'countries', 'countryNames'));
    }

    /**
     * Actualizar artículo
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'country' => 'nullable|string|size:2',
            'keywords' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
        ]);

        // Convertir keywords de string a array
        if ($request->filled('keywords')) {
            $validated['keywords'] = array_map('trim', explode(',', $request->keywords));
        }

        // Si se está publicando, agregar la fecha
        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Artículo actualizado exitosamente');
    }

    /**
     * Publicar artículo
     */
    public function publish(Post $post)
    {
        $this->blogGenerator->publishPost($post);

        return back()->with('success', 'Artículo publicado exitosamente');
    }

    /**
     * Archivar artículo
     */
    public function archive(Post $post)
    {
        $this->blogGenerator->archivePost($post);

        return back()->with('success', 'Artículo archivado');
    }

    /**
     * Eliminar artículo
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Artículo eliminado');
    }

    /**
     * Nombres de países
     */
    protected function getCountryNames(): array
    {
        return [
            'es' => 'España',
            'mx' => 'México',
            'ar' => 'Argentina',
            'co' => 'Colombia',
            'cl' => 'Chile',
            'pe' => 'Perú',
            'uy' => 'Uruguay',
        ];
    }
}
