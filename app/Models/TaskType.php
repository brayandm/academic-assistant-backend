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

    public function userHasQuota($user)
    {
        $quotas = $user->quotas()->get();

        $aiModels = $this->aiModels()->get();

        foreach ($aiModels as $aiModel) {

            $hasQuota = false;

            foreach ($quotas as $quota) {
                if ($quota->id == $aiModel->id) {
                    if ($quota->pivot->quota > 0) {
                        $hasQuota = true;
                    }
                }
            }

            if (! $hasQuota) {
                return false;
            }
        }

        return true;
    }
}
