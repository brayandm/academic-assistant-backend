<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function machineLearningTasks()
    {
        return $this->belongsToMany(MachineLearningTask::class, 'ai_models_usage', 'ai_model_id', 'machine_learning_task_id')->withTimestamps();
    }

    public function taskTypes()
    {
        return $this->belongsToMany(TaskType::class)->withTimestamps();
    }
}
