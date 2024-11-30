<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str; // For generating unique strings
use Jantinnerezo\LivewireAlert\LivewireAlert; // Import LivewireAlert
use Illuminate\Database\QueryException; // For handling database query exceptions

class Stock extends Model
{
    use HasFactory, LivewireAlert;

    protected $fillable = [
        'product_id',
        'quantity',
        'price_per_unit',
        'output_per_unit',
        'available_servings',
        'stock_batch_number',
    ];

    /**
     * Automatically handle events on model boot.
     */
    protected static function booted()
    {
        // Automatically generate batch number and calculate servings before creating stock
        static::creating(function ($stock) {
            $stock->generateUniqueBatchNumber();
            $stock->calculateAvailableServings();
        });

        // Log serving calculations after creation
        static::created(function ($stock) {
            try {
                $stock->calculateAvailableServings();
            } catch (\Exception $e) {
                Log::error('Error recalculating available servings for stock ID ' . $stock->id . ': ' . $e->getMessage());
            }
        });
    }

    /**
     * Calculate available servings based on quantity and output per unit.
     */
    public function calculateAvailableServings()
    {
        $this->available_servings = $this->quantity * $this->output_per_unit;
    }

    /**
     * Generate a unique batch number.
     */
    public function generateUniqueBatchNumber()
    {
        // Ensure uniqueness using random string and timestamp
        $this->stock_batch_number = 'BATCH-' . Str::upper(Str::random(4)) . '-' . now()->format('YmdHis');
    }

    /**
     * Relationship: Stock belongs to a product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Stock has many sales.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get total sales count without saving to DB.
     */
    public function getSalesCount()
    {
        return $this->sales->sum('quantity');
    }

    /**
     * Get total quantity deducted without saving to DB.
     */
    public function getQuantityDeducted()
    {
        return $this->sales->sum('quantity');
    }

    /**
     * Deduct stock when a sale is recorded.
     */
    public function deductStock($sale)
    {
        try {
            if ($sale->quantity > $this->available_servings) {
                // Handle excess demand
                $excessQuantity = $sale->quantity - $this->available_servings;

                ExcessDemand::create([
                    'product_id' => $sale->product_id,
                    'sale_id' => $sale->id,
                    'requested_quantity' => $sale->quantity,
                    'available_servings' => $this->available_servings,
                    'excess_quantity' => $excessQuantity,
                ]);

                $this->available_servings = 0;
            } else {
                // Deduct stock normally
                $this->available_servings -= $sale->quantity;
            }

            $this->save(); // Save updated stock details
        } catch (QueryException $e) {
            Log::error('Database error while deducting stock: ' . $e->getMessage());
            $this->alert('error', 'Error deducting stock: ' . $e->getMessage(), ['toast' => false]);
        } catch (\Exception $e) {
            Log::error('Error deducting stock: ' . $e->getMessage());
            $this->alert('error', 'Error deducting stock: ' . $e->getMessage(), ['toast' => false]);
        }
    }
}
