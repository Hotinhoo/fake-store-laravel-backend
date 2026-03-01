<?php

namespace App\Filters\Products;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function __construct(protected ?string $search) {}
    
    public function handle(Builder $query, Closure $next)
    {
        if (! $this->search) {
            return $next($query); // Se não enviou o filtro, passa para o próximo
        }

        // Filtro search (título parcial) usando índice fulltext para performance
        $query->whereFullText('title', $this->search);

        return $next($query);
    }
}