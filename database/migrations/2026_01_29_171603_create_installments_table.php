<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number'); // 1, 2, 3, etc.
            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->text('description')->nullable(); // e.g., "Monthly Installment 1", "On Confirmation"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('installments');
    }
};