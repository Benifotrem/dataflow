<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Mail::extend('brevo', function (array $config) {
            // Get API key from system settings (encrypted) or fallback to env
            $apiKey = SystemSetting::get('brevo_api_key')
                ?? config('services.brevo.api_key');

            return new BrevoApiTransport($apiKey);
        });
    }
}
