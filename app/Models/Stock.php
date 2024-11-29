<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // For generating unique strings
use Jantinnerezo\LivewireAlert\LivewireAlert; // Import LivewireAlert
use Illuminate\Database\QueryException; // For handling database query exceptions

class Stock extends Model
{
    use HasFactory, LivewireAlert; // Include LivewireAlert

    protected $fillable = [
        'product_id',
        'quantity',
        'price_per_unit',
        'output_per_unit',
        'available_servings',
        'stock_batch_number', // Ensure this is added to your fillable array
    ];

    protected static function booted()
    {
        static::creating(function ($stock) {
            try {
                $stock->generateUniqueBatchNumber(); // Generate a unique batch number before saving
                $stock->calculateAvailableServings();
            } catch (\Exception $e) {
                // If an error occurs during batch number generation, show an alert
                $stock->alert('error', 'Error generating batch number: ' . $e->getMessage(), ['toast' => false]);
            }
        });

        static::created(function ($stock) {
            try {
                $stock->calculateAvailableServings();
            } catch (\Exception $e) {
                // Log error if any issue occurs during the calculation of available servings
                Log::error('Error calculating available servings for stock ID ' . $stock->id . ': ' . $e->getMessage());
                $stock->alert('error', 'Error calculating available servings: ' . $e->getMessage(), ['toast' => false]);
            }
        });
    }

    // This method calculates the available servings based on quantity and output per unit
    public function calculateAvailableServings()
    {
        try {
            $this->available_servings = $this->quantity * $this->output_per_unit;
            $this->save(); // Try saving the stock after calculation
        } catch (\Exception $e) {
            // Log error if saving fails
            Log::error('Error saving available servings for stock ID ' . $this->id . ': ' . $e->getMessage());
            $this->alert('error', 'Error saving available servings: ' . $e->getMessage(), ['toast' => false]);
        }
    }

    // This method generates a unique batch number
    public function generateUniqueBatchNumber()
    {
        try {
            // Generate a unique batch number without UUID, using random string + timestamp
            $this->stock_batch_number = 'BATCH-' . Str::upper(Str::random(4)) . '-' . now()->format('YmdHis');
        } catch (\Exception $e) {
            // Handle error gracefully
            Log::error('Error while generating batch number for stock ID ' . $this->id . ': ' . $e->getMessage());
            throw new \Exception('Error while generating batch number: ' . $e->getMessage());
        }
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
                $stock->save(); // Save the stock after deduction
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
                $stock->save(); // Save after adjusting available servings
            }
        } catch (QueryException $e) {
            // Log database query errors (e.g., foreign key constraint violations)
            Log::error('Database query error while deducting stock for sale ID ' . $sale->id . ': ' . $e->getMessage());
            $this->alert('error', 'Database query error while deducting stock: ' . $e->getMessage(), ['toast' => false]);
        } catch (\Exception $e) {
            // Catch other general errors
            Log::error('Error deducting stock for sale ID ' . $sale->id . ': ' . $e->getMessage());
            $this->alert('error', 'Error deducting stock: ' . $e->getMessage(), ['toast' => false]);
        }
    }
}
