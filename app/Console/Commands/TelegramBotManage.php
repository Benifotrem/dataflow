<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramBotManage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:manage
                            {action : La acción a realizar (setup, info, delete, link)}
                            {--email= : Email del usuario para generar código de vinculación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar el bot de Telegram (webhook, información, vinculación)';

    protected TelegramService $telegramService;

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService): int
    {
        $this->telegramService = $telegramService;
        $action = $this->argument('action');

        return match ($action) {
            'setup' => $this->setupWebhook(),
            'info' => $this->getWebhookInfo(),
            'delete' => $this->deleteWebhook(),
            'link' => $this->generateLinkCode(),
            'me' => $this->getBotInfo(),
            default => $this->error("Acción no válida. Usa: setup, info, delete, link, me"),
        };
    }

    /**
     * Configurar el webhook
     */
    protected function setupWebhook(): int
    {
        $this->info('Configurando webhook de Telegram...');

        $webhookUrl = config('services.telegram.webhook_url');

        if (!$webhookUrl) {
            $this->error('La URL del webhook no está configurada en .env (TELEGRAM_WEBHOOK_URL)');
            return 1;
        }

        $this->info("URL del webhook: {$webhookUrl}");

        $result = $this->telegramService->setWebhook($webhookUrl);

        if ($result['success']) {
            $this->info('✅ Webhook configurado correctamente');
            $this->line('');
            $this->line('El bot ahora puede recibir actualizaciones en:');
            $this->line($webhookUrl);
            return 0;
        } else {
            $this->error('❌ Error al configurar webhook:');
            $this->error($result['message']);
            return 1;
        }
    }

    /**
     * Obtener información del webhook
     */
    protected function getWebhookInfo(): int
    {
        $this->info('Obteniendo información del webhook...');
        $this->line('');

        $result = $this->telegramService->getWebhookInfo();

        if ($result['success']) {
            $data = $result['data'];

            $this->table(
                ['Campo', 'Valor'],
                [
                    ['URL', $data['url'] ?? 'No configurado'],
                    ['Has Custom Certificate', $data['has_custom_certificate'] ? 'Sí' : 'No'],
                    ['Pending Update Count', $data['pending_update_count'] ?? 0],
                    ['Last Error Date', $data['last_error_date'] ?? 'N/A'],
                    ['Last Error Message', $data['last_error_message'] ?? 'N/A'],
                    ['Max Connections', $data['max_connections'] ?? 'N/A'],
                    ['Allowed Updates', implode(', ', $data['allowed_updates'] ?? [])],
                ]
            );

            if (isset($data['last_error_message'])) {
                $this->warn('⚠️  Hay errores en el webhook. Revisa la configuración.');
            } else {
                $this->info('✅ Webhook funcionando correctamente');
            }

            return 0;
        } else {
            $this->error('❌ Error al obtener información del webhook:');
            $this->error($result['message']);
            return 1;
        }
    }

    /**
     * Eliminar el webhook
     */
    protected function deleteWebhook(): int
    {
        if (!$this->confirm('¿Estás seguro de que deseas eliminar el webhook?')) {
            $this->info('Operación cancelada');
            return 0;
        }

        $this->info('Eliminando webhook...');

        $result = $this->telegramService->deleteWebhook();

        if ($result['success']) {
            $this->info('✅ Webhook eliminado correctamente');
            $this->warn('⚠️  El bot no recibirá actualizaciones hasta que configures un nuevo webhook');
            return 0;
        } else {
            $this->error('❌ Error al eliminar webhook:');
            $this->error($result['message']);
            return 1;
        }
    }

    /**
     * Generar código de vinculación
     */
    protected function generateLinkCode(): int
    {
        $email = $this->option('email');

        if (!$email) {
            $email = $this->ask('Ingresa el email del usuario');
        }

        if (!$email) {
            $this->error('Debes proporcionar un email');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return 1;
        }

        if ($user->hasTelegramLinked()) {
            $this->warn("⚠️  Este usuario ya tiene Telegram vinculado");
            $this->line("Telegram ID: {$user->telegram_id}");
            $this->line("Username: {$user->telegram_username}");

            if (!$this->confirm('¿Deseas generar un nuevo código para cambiar la vinculación?')) {
                return 0;
            }
        }

        $code = $this->telegramService->generateLinkCode($user);

        $this->line('');
        $this->info('✅ Código de vinculación generado');
        $this->line('');
        $this->line('═══════════════════════════════════════');
        $this->line("  Usuario: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line('');
        $this->line("  Código: {$code}");
        $this->line('═══════════════════════════════════════');
        $this->line('');
        $this->warn('⏰ El código expira en 15 minutos');
        $this->line('');
        $this->info('Instrucciones para el usuario:');
        $this->line('1. Abre Telegram y busca el bot de Dataflow');
        $this->line('2. Inicia el bot con /start');
        $this->line("3. Envía el código: {$code}");

        return 0;
    }

    /**
     * Obtener información del bot
     */
    protected function getBotInfo(): int
    {
        $this->info('Obteniendo información del bot...');
        $this->line('');

        $botInfo = $this->telegramService->getMe();

        if ($botInfo) {
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $botInfo['id']],
                    ['Nombre', $botInfo['first_name']],
                    ['Username', '@' . $botInfo['username']],
                ]
            );

            $this->line('');
            $this->info("Para vincular tu cuenta, busca @{$botInfo['username']} en Telegram");

            return 0;
        } else {
            $this->error('❌ Error al obtener información del bot');
            $this->error('Verifica que el TELEGRAM_BOT_TOKEN esté configurado correctamente');
            return 1;
        }
    }
}
