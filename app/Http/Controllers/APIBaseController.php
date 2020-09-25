<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;

class APIBaseController extends Controller
{
    /**
     * @param null $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message = 'OK', $data = null, $code = 200)
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

        if($errors instanceof MessageBag)
        {
            $errors = $this->extractErrorMessageFromArray($errors->getMessages());

        } else if(is_array($errors))
        {
            $errors = $this->extractErrorMessageFromArray($errors);
        }

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

    protected function extractErrorMessageFromArray($errors)
    {
        $err = [];

        foreach ($errors as $key => $value) {

            $err[]  = is_array($value) ? implode("\n", $value) : $value;
        }
        return implode("\n", $err);
    }

    protected function perginationPerPage(): int
    {
        $perPage = request('per_page') ?: 100;

        if($perPage > 100) $perPage = 100;

        return $perPage;
    }

    protected function getPagingData(LengthAwarePaginator $paginator, callable $collection = null )
    {

        return [
            'data' => $collection ?  $collection($paginator->items()) : $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'first_page_url' => $paginator->url(1),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
