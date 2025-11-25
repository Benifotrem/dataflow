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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_link')->nullable()->after('price');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('payment_link');
            $table->string('payment_transaction_id')->nullable()->after('payment_status');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_transaction_id');
            $table->timestamp('payment_notified_at')->nullable()->after('payment_completed_at');
            $table->json('payment_metadata')->nullable()->after('payment_notified_at');

            $table->index('payment_status');
            $table->index('payment_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payment_transaction_id']);
            $table->dropColumn([
                'payment_link',
                'payment_status',
                'payment_transaction_id',
                'payment_completed_at',
                'payment_notified_at',
                'payment_metadata',
            ]);
        });
    }
};
