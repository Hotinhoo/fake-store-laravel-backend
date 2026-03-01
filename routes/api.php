<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStatsController;
use App\Http\Controllers\ProductImportController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {

    // Importar Produtos da FakeStoreAPI
    Route::post('/import', ProductImportController::class);

    // Estatísticas
    Route::get('/stats', ProductStatsController::class);

    // Listagem de Produtos com filtros e paginação
    Route::get('/', [ProductController::class, 'index']);

    // Detalhes do produto
    Route::get('/{product}', [ProductController::class, 'show']);

    // Atualização Parcial
    Route::patch('/{product}', [ProductController::class, 'update']);

    // Remoção (Soft Delete)
    Route::delete('/{product}', [ProductController::class, 'destroy']);

});