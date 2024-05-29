<?php

declare(strict_types=1);

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ApiResponse
{
    public static function error(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'text' => $message,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function success(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
