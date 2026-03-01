<?php

namespace App\Http\Controllers;

use App\Services\ProductStatisticsService;
use Illuminate\Http\JsonResponse;

class ProductStatsController extends Controller
{
    /**
     * Retorna estatísticas gerais do sistema.
     */
    public function __invoke(ProductStatisticsService $statsService): JsonResponse
    {
        // O Service resolve os cálculos e o cache
        $stats = $statsService->getStats();

        return response()->json($stats, 200);
    }
}