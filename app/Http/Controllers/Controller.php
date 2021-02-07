<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function apiResponse(int $status_code = 200, string $message = "Success", array $data = [], array $headers = [])
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status_code, $headers);
    }
    public function apiErrorResponse(int $status_code, string $message = "Error while processing", array $errors = [], array $headers = [])
    {
        return response()->json([
            'message' => $message,
            'errors' => !empty($errors) ? $errors : [$message],
        ], $status_code, $headers);
    }
}
