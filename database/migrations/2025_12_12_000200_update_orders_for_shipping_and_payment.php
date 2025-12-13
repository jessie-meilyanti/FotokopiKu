<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_name')->nullable()->after('user_id');
            $table->string('recipient_phone')->nullable()->after('recipient_name');
            $table->text('shipping_address')->nullable()->after('recipient_phone');
            $table->string('payment_bank')->nullable()->after('payment_method');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->string('payment_proof_path')->nullable()->after('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_name',
                'recipient_phone',
                'shipping_address',
                'payment_bank',
                'payment_reference',
                'payment_proof_path',
            ]);
        });
    }
};

