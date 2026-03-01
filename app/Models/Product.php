<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'external_id',
        'title',
        'price',
        'description',
        'category',
        'image',
        'rating_rate',
        'rating_count',
        'update_log'
    ];

    /**
     * Casts para garantir que os tipos de dados estejam corretos ao acessar
     */
    protected $casts = [
        'price' => 'decimal:2',
        'rating_rate' => 'decimal:2',
        'rating_count' => 'integer',
        'update_log' => 'array',
        'external_id' => 'integer'
    ];

}
