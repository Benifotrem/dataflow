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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('telegram_id')->nullable()->unique()->after('is_admin');
            $table->string('telegram_username')->nullable()->after('telegram_id');
            $table->bigInteger('telegram_chat_id')->nullable()->after('telegram_username');
            $table->timestamp('telegram_linked_at')->nullable()->after('telegram_chat_id');

            $table->index('telegram_id');
            $table->index('telegram_chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['telegram_id']);
            $table->dropIndex(['telegram_chat_id']);
            $table->dropColumn(['telegram_id', 'telegram_username', 'telegram_chat_id', 'telegram_linked_at']);
        });
    }
};
