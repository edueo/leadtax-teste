<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'mercadolivre_id',
        'title',
        'price',
        'original_price',
        'discount_percentage',
        'available_quantity',
        'sold_quantity',
        'condition',
        'permalink',
        'thumbnail',
        'seller_id',
        'seller_name',
        'seller_rating',
        'category_id',
        'category_name',
        'description',
        'attributes',
        'shipping_info',
        'last_updated',
    ];

    protected $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'discount_percentage' => 'float',
        'available_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'seller_rating' => 'float',
        'attributes' => 'array',
        'shipping_info' => 'array',
        'last_updated' => 'datetime',
    ];
}
