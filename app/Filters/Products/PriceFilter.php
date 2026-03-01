<?php

namespace App\Filters\Products;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class PriceFilter
{
    public function __construct(
        protected ?float $priceMin,
        protected ?float $priceMax
    ) {}

    public function handle(Builder $query, Closure $next)
    {
        if ($this->priceMin) {
            $query->where('price', '>=', $this->priceMin);
        }

        if ($this->priceMax) {
            $query->where('price', '<=', $this->priceMax);
        }

        return $next($query);
    }
}