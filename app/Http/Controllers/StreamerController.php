<?php

namespace App\Http\Controllers;

use App\Services\StreamerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamerController extends Controller
{
    private StreamerService $streamerService;

    public function __construct(StreamerService $streamerService)
    {
        $this->streamerService = $streamerService;
    }

    public function accessControl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:256',
            'task_type' => 'required|string|max:256',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = $this->streamerService->accessControl($request->token, $request->task_type);

        if ($response === null) {
            return response()->json(['user_id' => null, 'quota' => null, 'message' => 'Access denied'], 403);
        }

        return response()->json(['user_id' => $response['user']->id, 'quota' => $response['quota'], 'message' => 'Access granted'], 200);
    }

    public function createTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|string|max:256',
            'task_type' => 'required|string|max:256',
            'task_status' => 'required|string|max:256',
            'user_id' => 'required|integer',
            'input_type' => 'required|string|max:256',
            'input' => 'required|string|max:5000',
            'result_type' => 'required|string|max:256',
            'result' => 'required|string|max:10000',
            'ai_models' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $this->streamerService->createTask($request->task_id, $request->task_type, $request->task_status, $request->user_id, $request->input_type, $request->input, $request->result_type, $request->result, json_decode($request->ai_models, true));
    }
}
