<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIBaseController extends Controller
{
    /**
     * @param null $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(string $message = 'OK', $data = null, int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response. It uses code 200 because "API client can't parse the error"
     * @param string $message
     * @param null $errors
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $message = 'Error message', $errors = null, int $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * @param null $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponseWithUser(string $message = 'OK', $data = null, int $code = 200)
    {
        $data['user'] = $this->user()->transform();

        return $this->successResponse($message, $data, $code);
    }

    /**
     * @return User|null
     */
    protected function user()
    {
        return auth()->user();
    }
}
