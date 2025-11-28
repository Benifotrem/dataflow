<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Suscribir a newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email no es válido',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar si ya existe
        $existing = Subscriber::where('email', $request->email)->first();

        if ($existing) {
            if ($existing->isActive()) {
                return back()->with('info', '¡Ya estás suscrito a nuestro newsletter!');
            } else {
                // Reactivar suscripción
                $existing->resubscribe();
                return back()->with('success', '¡Tu suscripción ha sido reactivada! Gracias por volver.');
            }
        }

        // Crear nueva suscripción
        Subscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'status' => 'active',
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
            'source' => $request->input('source', 'blog'),
        ]);

        return back()->with('success', '¡Gracias por suscribirte! Recibirás nuestras últimas publicaciones.');
    }

    /**
     * Desuscribirse (vía enlace en email)
     */
    public function unsubscribe(Request $request, $email)
    {
        $subscriber = Subscriber::where('email', $email)->first();

        if (!$subscriber) {
            return view('unsubscribe', [
                'message' => 'No encontramos tu email en nuestra lista de suscriptores.',
                'success' => false,
            ]);
        }

        if (!$subscriber->isActive()) {
            return view('unsubscribe', [
                'message' => 'Ya estás desuscrito de nuestro newsletter.',
                'success' => true,
            ]);
        }

        $subscriber->unsubscribe();

        return view('unsubscribe', [
            'message' => 'Te has desuscrito exitosamente. Lamentamos verte ir.',
            'success' => true,
        ]);
    }
}
