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
            'blog_auto_generation_enabled' => 'nullable',
            'blog_auto_publish' => 'nullable',
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

    /**
     * Mostrar configuración de email (Brevo)
     */
    public function email()
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        // Obtener settings como array [key => value]
        $settings = [];
        $settingRecords = Setting::where('group', 'email')->get();

        foreach ($settingRecords as $setting) {
            // Solo verificar existencia para encrypted, no devolver valor
            if ($setting->type === 'encrypted') {
                $settings[$setting->key] = $setting->value ? true : false;
            } else {
                $settings[$setting->key] = $setting->value;
            }
        }

        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Actualizar configuración de email (Brevo)
     */
    public function updateEmail(Request $request)
    {
        // Verificar permisos
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'owner') {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $validated = $request->validate([
            'brevo_api_key' => 'nullable|string',
            'email_from_name' => 'required|string|max:255',
            'email_from_address' => 'required|email|max:255',
            'email_welcome_enabled' => 'nullable',
            'email_document_limit_enabled' => 'nullable',
            'email_document_limit_threshold' => 'required|integer|min:50|max:100',
        ]);

        try {
            // Guardar API key (encriptada)
            if ($request->filled('brevo_api_key')) {
                Setting::set('brevo_api_key', $request->brevo_api_key, 'encrypted', 'email');
            }

            // Guardar configuración general
            Setting::set('email_from_name', $request->email_from_name, 'string', 'email');
            Setting::set('email_from_address', $request->email_from_address, 'string', 'email');
            Setting::set('email_welcome_enabled', $request->has('email_welcome_enabled'), 'boolean', 'email');
            Setting::set('email_document_limit_enabled', $request->has('email_document_limit_enabled'), 'boolean', 'email');
            Setting::set('email_document_limit_threshold', (int)$request->email_document_limit_threshold, 'integer', 'email');

            return back()->with('success', 'Configuración de email actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }
}
