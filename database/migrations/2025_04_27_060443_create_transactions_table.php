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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('discount_type')->nullable(); // 'percentage' or 'fixed'
            $table->decimal('total', 12, 2);
            $table->string('payment_method'); // 'cash' or 'non-cash'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
