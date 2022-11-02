<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function hasAuth(): bool
    {
        return request()->user() != null;
    }

    public function response($data, $errors = null,  $message = "", $status = 200): JsonResponse
    {
        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data,
            "errors" => $errors,
        ])->setStatusCode($status);
    }
    public function setResponse($data, $status = 200): JsonResponse
    {
        return $this->response($data, null,"", $status);
    }
    public function setErrorResponse($data, $status = 400): JsonResponse
    {
        return $this->response(null, $data,"", $status);
    }
    public function setErrorMessage($message, $status = 400): JsonResponse
    {
        return $this->response(null, null,$message, $status);
    }

    public function unauthorised(): JsonResponse
    {
        return $this->setErrorMessage('Unauthorised', 401);
    }
}
