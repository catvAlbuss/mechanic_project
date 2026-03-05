<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $fillable = [
        'id_category',
        'sku',
        'name',
        'brand',
        'cost_price',
        'sale_price',
        'stock',
        'factory_date',
        'made',
        'state',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }
}
