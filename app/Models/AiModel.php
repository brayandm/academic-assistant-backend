<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function engineTasks()
    {
        return $this->belongsToMany(EngineTask::class)->withTimestamps();
    }
}