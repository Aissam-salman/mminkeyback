<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse(mixed $result, string $message, int $statusCode = 200): JsonResponse {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }

    public function sendError(mixed $error, mixed $errorMessagesDebug = null, int $statusCode = 404): JsonResponse {
        $response = [
            'success' => false,
            'message' => $error,
            'errorsDebug' => $errorMessagesDebug ?? [], //TODO: before deploy in production remove this
        ];
        return response()->json($response, $statusCode);
    }
}
