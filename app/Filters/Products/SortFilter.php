<?php

namespace App\Filters\Products;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SortFilter
{
    public function __construct(
        protected ?string $sortBy,
        protected ?string $sortDir
    ) {}

    public function handle(Builder $query, Closure $next)
    {
        $column = $this->sortBy ?? 'id'; // Padrão
        $direction = $this->sortDir === 'desc' ? 'desc' : 'asc';

        $query->orderBy($column, $direction);

        return $next($query);
    }
}