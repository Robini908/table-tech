<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_id',
        'quantity',
        'price_per_unit',
        'total_price',
    ];

    // This is kept for future reference if you need to recalculate the total price
    public function calculateTotalPrice()
    {
        // Calculate total price as quantity * price per unit
        $this->total_price = $this->quantity * $this->price_per_unit;
        $this->save();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
