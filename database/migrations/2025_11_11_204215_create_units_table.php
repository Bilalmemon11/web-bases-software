<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('unit_no');
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->enum('status', ['available', 'sold', 'reserved'])->default('available');
            $table->timestamps();
            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
