<?php

namespace App\Http\Middleware;

use App\Facades\RequestManagerFacades;
use App\Models\AiModel;
use App\Models\MachineLearningTask;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EngineUsage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $task_id = $request->all()['task_id'];
        $ai_models = $request->all()['ai_models'];

        $machineLearningTask = MachineLearningTask::where('task_id', $task_id)->first();

        $user = User::find($machineLearningTask->user_id);

        Auth::login($user);

        foreach ($ai_models as $ai_model) {

            $aiModel = AiModel::firstOrCreate([
                'name' => $ai_model['name'],
                'option' => $ai_model['option'],
                'usage_type' => $ai_model['usage_type'],
            ]);

            $machineLearningTask->aiModels()->attach($aiModel->id, ['usage' => $ai_model['usage']]);

            $quota = $user->quotas()->where('ai_model_id', $aiModel->id)->first();

            $user->quotas()->updateExistingPivot($aiModel->id, ['quota' => $quota->pivot->quota - $ai_model['usage']]);

            $quota->save();
        }

        return $next($request);
    }
}
