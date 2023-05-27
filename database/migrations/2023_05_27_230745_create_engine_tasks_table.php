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
        Schema::create('engine_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id');
            $table->string('task_type');
            $table->string('task_status');
            $table->foreignId('user_id')->constrained();
            $table->string('result_type');
            $table->string('result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engine_tasks');
    }
};
