<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineLearningTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiModels()
    {
        return $this->belongsToMany(AiModel::class, 'ai_models_usage', 'machine_learning_task_id', 'ai_model_id')->withTimestamps();
    }
}
