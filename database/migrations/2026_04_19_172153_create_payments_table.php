<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('notes')->nullable();

            // Payment method details
            $table->enum('method', ['cash', 'cheque', 'bank_transfer', 'online'])->default('cash');

            // Bank / Cheque details (applicable when method is cheque or bank_transfer)
            $table->string('bank_name')->nullable();
            $table->string('cheque_no')->nullable();       // For cheque payments
            $table->string('account_no')->nullable();      // For bank transfer
            $table->string('transaction_ref')->nullable(); // For online / bank transfer

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};