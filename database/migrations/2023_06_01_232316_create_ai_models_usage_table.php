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
        Schema::create('ai_models_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_learning_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('ai_model_id')->constrained()->onDelete('cascade');
            $table->integer('usage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models_usage');
    }
};
