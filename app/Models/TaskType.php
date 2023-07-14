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

    public function userQuota($user)
    {
        $quotas = $user->quotas()->get();

        $aiModels = $this->aiModels()->get();

        $modelQuotas = [];

        foreach ($aiModels as $aiModel) {

            $quotaLeft = 0;

            foreach ($quotas as $quota) {
                if ($quota->id == $aiModel->id) {
                    if ($quota->pivot->quota > 0) {
                        $quotaLeft = $quota->pivot->quota;
                        break;
                    }
                }
            }

            if ($quotaLeft <= 0) {
                return null;
            }

            $modelQuotas[$aiModel->name] = $quotaLeft;
        }

        return $modelQuotas;
    }

    public function userHasQuota($user)
    {
        return $this->userQuota($user) !== null;
    }
}
