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
        Schema::create('machine_learning_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id');
            $table->foreignId('task_type_id')->constrained();
            $table->string('task_status');
            $table->foreignId('user_id')->constrained();
            $table->string('input_type');
            $table->string('input', 5000);
            $table->string('result_type');
            $table->string('result', 10000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_learning_tasks');
    }
};
