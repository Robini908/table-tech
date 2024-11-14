<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'price_per_unit',
        'output_per_unit',
        'available_servings',
    ];

    protected static function booted()
    {
        static::created(function ($stock) {
            $stock->calculateAvailableServings();
        });
    }

    // This method calculates the available servings based on quantity and output per unit
    public function calculateAvailableServings()
    {
        $this->available_servings = $this->quantity * $this->output_per_unit;
        $this->save();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Get total sales count without saving to DB
    public function getSalesCount()
    {
        return $this->sales->sum('quantity');
    }

    // Get total quantity deducted without saving to DB
    public function getQuantityDeducted()
    {
        return $this->sales->sum('quantity');
    }

    // Method to update stock when a sale is recorded
    public function deductStock($sale)
    {
        try {
            $stock = Stock::find($sale->stock_id);

            if ($stock && $sale->quantity <= $stock->available_servings) {
                // Deduct stock and update available servings
                $stock->available_servings -= $sale->quantity;
                $stock->save();
            } else {
                // Handle excess demand logic
                $excessQuantity = $sale->quantity - $stock->available_servings;

                ExcessDemand::create([
                    'product_id' => $sale->product_id,
                    'sale_id' => $sale->id,
                    'requested_quantity' => $sale->quantity,
                    'available_servings' => $stock->available_servings,
                    'excess_quantity' => $excessQuantity,
                ]);

                $stock->available_servings = 0;  // Set available servings to zero
                $stock->save();
            }
        } catch (\Exception $e) {
            // Log error or display a notification
            $this->alert('error', 'Error deducting stock: ' . $e->getMessage(), ['toast' => false]);
        }
    }
}
