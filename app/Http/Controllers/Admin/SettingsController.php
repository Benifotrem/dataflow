<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Mostrar configuración del blog
     */
    public function blog()
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        // Obtener settings como array [key => value]
        $settings = [];
        $settingRecords = Setting::where('group', 'blog')->get();

        foreach ($settingRecords as $setting) {
            // Solo verificar existencia para encrypted, no devolver valor
            if ($setting->type === 'encrypted') {
                $settings[$setting->key] = $setting->value ? true : false;
            } else {
                $settings[$setting->key] = $setting->value;
            }
        }

        return view('admin.settings.blog', compact('settings'));
    }

    /**
     * Actualizar configuración del blog
     */
    public function updateBlog(Request $request)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $validated = $request->validate([
            'openrouter_api_key' => 'nullable|string',
            'pexels_api_key' => 'nullable|string',
            'blog_generation_model' => 'required|string',
            'blog_min_words' => 'required|integer|min:500|max:5000',
            'blog_max_words' => 'required|integer|min:500|max:5000',
            'blog_author_name' => 'nullable|string|max:255',
            'blog_auto_generation_enabled' => 'nullable|boolean',
            'blog_auto_publish' => 'nullable|boolean',
        ]);

        try {
            // Guardar API keys (encriptadas)
            if ($request->filled('openrouter_api_key')) {
                Setting::set('openrouter_api_key', $request->openrouter_api_key, 'encrypted', 'blog');
            }

            if ($request->filled('pexels_api_key')) {
                Setting::set('pexels_api_key', $request->pexels_api_key, 'encrypted', 'blog');
            }

            // Guardar configuración general
            Setting::set('blog_generation_model', $request->blog_generation_model, 'string', 'blog');
            Setting::set('blog_min_words', (int)$request->blog_min_words, 'integer', 'blog');
            Setting::set('blog_max_words', (int)$request->blog_max_words, 'integer', 'blog');
            Setting::set('blog_author_name', $request->blog_author_name ?: 'César Ruzafa', 'string', 'blog');

            // Configuración de generación automática
            Setting::set('blog_auto_generation_enabled', $request->has('blog_auto_generation_enabled'), 'boolean', 'blog');
            Setting::set('blog_auto_publish', $request->has('blog_auto_publish'), 'boolean', 'blog');

            return back()->with('success', 'Configuración actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }
}
