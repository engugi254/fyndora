<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            // User details from your checkout form
            $table->string('customer_name')->nullable()->after('id');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('amount_paid');
            
            // Date & time when payment was confirmed (very useful for admin)
            $table->timestamp('paid_at')->nullable()->after('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'amount_paid',
                'customer_phone',
                'paid_at'
            ]);
        });
    }
};