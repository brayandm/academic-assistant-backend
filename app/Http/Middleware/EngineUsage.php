<?php

namespace App\Http\Middleware;

use App\Models\AiModel;
use App\Models\EngineTask;
use Closure;
use Illuminate\Http\Request;
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

        foreach ($ai_models as $ai_model) {

            $engineTask = EngineTask::where('task_id', $task_id)->first();

            $aiModel = AiModel::firstOrCreate([
                'name' =>  $ai_model['name'],
                'option' => $ai_model['option'],
                'usage_type'=> $ai_model['usage_type'],
            ]);

            $engineTask->aiModels()->attach($aiModel->id, ['usage' => $ai_model['usage']]);
        }

        return $next($request);
    }
}
