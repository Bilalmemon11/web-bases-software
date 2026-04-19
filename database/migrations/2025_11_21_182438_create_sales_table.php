<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->virtualAs('total_amount - paid_amount - discount');
            $table->enum('status', ['reserved', 'sold', 'cancelled'])->default('reserved');
            $table->string('payment_method')->nullable();
            $table->date('sale_date')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
