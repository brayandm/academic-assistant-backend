<?php

namespace App\Http\Controllers;

use App\Services\EngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EngineController extends Controller
{
    private EngineService $engineService;

    public function __construct(EngineService $engineService)
    {
        $this->engineService = $engineService;
    }

    public function webhookTranslate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:256',
            'task_id' => 'required|string|max:256',
            'result.text' => 'required|string|max:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $this->engineService->webhookTranslate($request->task_id, $request->status, $request->result['text']);
    }
}
