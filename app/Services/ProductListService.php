<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductListService
{
    public function list(array $filters): LengthAwarePaginator
    {
        // Instancia a query inicial
        $query = Product::query();

        // Monta o array de filtros (Pipes)
        $pipes = [
            new \App\Filters\Products\CategoryFilter($filters['category'] ?? null),
            new \App\Filters\Products\SearchFilter($filters['search'] ?? null),
            new \App\Filters\Products\PriceFilter($filters['price_min'] ?? null, $filters['price_max'] ?? null),
            new \App\Filters\Products\RatingFilter($filters['rating_min'] ?? null),
            new \App\Filters\Products\SortFilter($filters['sort_by'] ?? null, $filters['sort_dir'] ?? null),
        ];

        // Passa a query pelo pipeline
        $query = app(Pipeline::class)
            ->send($query)
            ->through($pipes)
            ->thenReturn();

        // Define 15 como padrão se não for enviado na request
        $perPage = $filters['per_page'] ?? 15;

        // Retorna os dados paginados
        return $query->paginate($perPage);
    }
}