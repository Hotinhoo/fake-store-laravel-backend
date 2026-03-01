<?php

namespace App\Filters\Products;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class CategoryFilter
{
    public function __construct(protected ?string $category) {}

    public function handle(Builder $query, Closure $next)
    {
        // Se nenhuma categoria foi enviada, passa para o próximo pipe
        if (! $this->category) {
            return $next($query);
        }

        // Aplica o filtro exato na coluna 'category'
        $query->where('category', $this->category);

        return $next($query);
    }
}