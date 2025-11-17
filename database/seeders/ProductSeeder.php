<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing products (optional)
        Product::truncate();

        // Insert your initial products
        $products = [
            ['name' => 'Wireless Headphones', 'price' => 3500, 'image' => 'headphones.jpg'],
            ['name' => 'Smart Watch', 'price' => 5500, 'image' => 'watch.jpg'],
            ['name' => 'Bluetooth Speaker', 'price' => 4000, 'image' => 'speaker.jpg'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
