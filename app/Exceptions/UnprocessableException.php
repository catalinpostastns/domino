<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class UnprocessableException extends Exception implements RenderExceptionInterface
{
    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json(["message" => $this->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
