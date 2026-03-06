<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Standard success envelope for single resource or flat data.
     */
    protected function success(mixed $data, string $message = '', int $status = 200): JsonResponse
    {
        $payload = ['success' => true, 'data' => $data];
        if ($message !== '') {
            $payload['message'] = $message;
        }
        return response()->json($payload, $status);
    }

    /**
     * Standard success envelope for paginated results.
     * Preserves Laravel's pagination meta (current_page, last_page, total, etc.).
     */
    protected function paginated(LengthAwarePaginator $paginator): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    /**
     * Standard error envelope.
     */
    protected function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $status);
    }
}
