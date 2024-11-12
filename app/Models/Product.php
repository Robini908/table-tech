<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia

{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'quantity',
        'cost_price',
        'selling_price',
        'status',
    ];

    // Relationship to category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // For handling media attachments (images)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')->singleFile();
    }

    // Optional: Get the first image from media collection
    public function getFirstImageAttribute()
    {
        return $this->getFirstMediaUrl('product_images');
    }

    // Optional: Get all images from media collection
    public function getAllImagesAttribute()
    {
        return $this->getMedia('product_images');
    }
}
