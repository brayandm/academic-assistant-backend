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
        Schema::create('ai_model_engine_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engine_task_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('ai_model_engine_task');
    }
};
