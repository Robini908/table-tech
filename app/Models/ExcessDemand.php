<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcessDemand extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sale_id',
        'requested_quantity',
        'available_servings',
        'excess_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}

