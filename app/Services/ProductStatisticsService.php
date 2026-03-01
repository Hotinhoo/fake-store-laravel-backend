<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductStatisticsService
{
    /**
     * Retorna as estatísticas consolidadas dos produtos.
     */
    public function getStats(): array
    {
        // Armazena as estatísticas em cache por 1 hora (3600 segundos)
        return Cache::remember('products_stats', 3600, function () {
            
            return [
                // Total de produtos 
                'total_products'   => Product::count(), 
                
                // Preço médio formatado 
                'average_price'    => round(Product::avg('price') ?? 0, 2), 
                
                // Maior preço [cite: 75]
                'highest_price'    => (float) Product::max('price') ?? 0, 
                
                // Menor preço [cite: 76]
                'lowest_price'     => (float) Product::min('price') ?? 0, 
                
                // Contagem por categoria
                // Retorna algo como: {"electronics": 6, "jewelery": 4}
                'categories_count' => Product::select('category', DB::raw('count(*) as count'))
                                        ->groupBy('category')
                                        ->pluck('count', 'category')
                                        ->toArray(),
            ];
            
        });
    }
}
