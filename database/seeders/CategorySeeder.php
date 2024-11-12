<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Create a Faker instance
        $faker = Faker::create();

        // Seed 10 categories with random names
        foreach (range(1, 10) as $index) {
            Category::create([
                'name' => $faker->word, // Random category name
            ]);
        }
    }
}
