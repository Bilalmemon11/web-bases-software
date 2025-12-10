<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('expense_date')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('expenses');
    }
};
