<?php

namespace App\Console\Commands;

use App\Models\TelegramConversation;
use App\Models\User;
use App\Services\TelegramConversationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ExportTelegramConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:export-conversations
                            {--email= : Email del estudio de contaduría}
                            {--user= : ID del usuario específico}
                            {--since= : Exportar conversaciones desde esta fecha (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exportar y enviar conversaciones de Telegram por email al estudio de contaduría';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Exportando conversaciones de Telegram...');

        $email = $this->option('email') ?? config('mail.from.address');
        $userId = $this->option('user');
        $since = $this->option('since');

        if (!$email) {
            $this->error('❌ No se especificó un email. Usa --email=correo@ejemplo.com');
            return 1;
        }

        // Obtener usuarios con conversaciones
        $query = TelegramConversation::query()
            ->select('user_id', 'chat_id')
            ->distinct();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $conversations = $query->get();

        if ($conversations->isEmpty()) {
            $this->warn('⚠️  No se encontraron conversaciones para exportar.');
            return 0;
        }

        $this->info("📊 Se encontraron " . $conversations->count() . " conversaciones.");

        $conversationService = app(TelegramConversationService::class);
        $exports = [];

        foreach ($conversations as $conv) {
            $user = User::find($conv->user_id);
            if (!$user) {
                continue;
            }

            $export = $conversationService->exportConversation($user, $conv->chat_id);
            $exports[] = $export;

            $this->info("  ✓ Exportada conversación de {$user->name} (Chat ID: {$conv->chat_id})");
        }

        // Enviar email
        try {
            Mail::send('emails.telegram-conversations', ['exports' => $exports], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Exportación de Conversaciones Telegram - ' . now()->format('d/m/Y H:i'));
            });

            $this->info("✅ Email enviado exitosamente a: {$email}");

            Log::info('Conversaciones exportadas y enviadas por email', [
                'email' => $email,
                'total_conversations' => count($exports),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            Log::error('Error al enviar email de conversaciones', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return 1;
        }
    }
}
