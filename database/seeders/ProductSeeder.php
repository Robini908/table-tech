<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Faker\Factory as Faker;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $product = Product::create([
                'name' => $faker->word,
                'category_id' => Category::inRandomOrder()->first()->id,
                'description' => $faker->sentence,
                'quantity' => $faker->numberBetween(1, 100),
                'cost_price' => $faker->randomFloat(2, 5, 100),
                'selling_price' => $faker->randomFloat(2, 10, 200),
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);

            $imagePath = storage_path('app/public/images/placeholder.jpg');

            if (file_exists($imagePath)) {
                $product->addMedia($imagePath)->toMediaCollection('product_images');
            } else {
                Log::warning("Placeholder image not found for product {$product->name}");
            }
        }
    }
}
