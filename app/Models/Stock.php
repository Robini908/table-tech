<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'cost_price',
        'selling_price',
        'expiry_date',
        'supplier_id',
        'batch_number',
        'images',
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'images' => 'array', // Cast JSON images to array
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function wasteManagement(): HasMany
    {
        return $this->hasMany(WasteManagement::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Calculate stock value automatically (cost_price * quantity)
    public function getStockValueAttribute()
    {
        return $this->cost_price * $this->quantity;
    }
}
