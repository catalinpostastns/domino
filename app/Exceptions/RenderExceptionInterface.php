<?php
namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

interface RenderExceptionInterface
{
    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse;
}
