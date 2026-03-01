<?php

namespace App\Filters\Products;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class RatingFilter
{
    public function __construct(protected ?float $ratingMin) {}

    public function handle(Builder $query, Closure $next)
    {
        // Usamos is_null para garantir que o valor 0 seja processado, 
        // já que o rating_rate pode ser entre 0 e 5.
        if (is_null($this->ratingMin)) {
            return $next($query);
        }

        // Aplica a condição de "maior ou igual"
        $query->where('rating_rate', '>=', $this->ratingMin);

        return $next($query);
    }
}