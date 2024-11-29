<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str; // For generating unique strings

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'price',
        'description',
        'category_id',
        'sku', // Add SKU to fillable array
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            $product->generateUniqueSku(); // Generate a unique SKU before saving
        });
    }

    // This method generates a unique SKU
    public function generateUniqueSku()
    {
        // Generate a unique SKU by combining a random string with the current timestamp
        $this->sku = 'SKU-' . Str::upper(Str::random(6)) . '-' . now()->format('YmdHis');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }
}
