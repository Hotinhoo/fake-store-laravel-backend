<?php

namespace App\Http\Controllers;

use App\Services\ProductImportService;
use Illuminate\Http\JsonResponse;

class ProductImportController extends Controller
{
    /**
     * Endpoint para importar produtos da API externa
     */
    public function __invoke(ProductImportService $importService): JsonResponse
    {
        $result = $importService->import();

        if (!$result['success']) {
            return response()->json([
                'error' => $result['message']
            ], 503);
        }

        return response()->json($result['data'], 200);
    }
}
