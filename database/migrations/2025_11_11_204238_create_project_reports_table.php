<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->enum('type', ['weekly', 'monthly', 'custom']);
            $table->datetime('generated_at');
            $table->string('file_path');
            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_reports');
    }
};
