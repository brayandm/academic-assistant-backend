<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;

    public function aiModels()
    {
        return $this->belongsToMany(AiModel::class)->withTimestamps();
    }

    public function userHasQuota()
    {
        return true;
    }
}
