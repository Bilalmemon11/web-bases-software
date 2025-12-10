<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('investment_amount', 15, 2)->default(0);
            $table->decimal('profit_share', 5, 2)->nullable();
            $table->enum('role', ['manager', 'investor'])->default('investor');
            $table->timestamps();

            $table->unique(['project_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
