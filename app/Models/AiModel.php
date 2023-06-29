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
        return $this->belongsToMany(MachineLearningTask::class)->withTimestamps();
    }
}
