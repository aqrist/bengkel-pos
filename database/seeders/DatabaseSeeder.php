<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@bengkel.com',
            'password' => bcrypt('password')
        ]);

        // Create categories
        $spareparts = Category::create(['name' => 'Sparepart', 'description' => 'Suku cadang motor']);
        $oli = Category::create(['name' => 'Oli', 'description' => 'Oli mesin dan pelumas']);
        $aksesoris = Category::create(['name' => 'Aksesoris', 'description' => 'Aksesoris motor']);

        // Create products
        Product::create([
            'name' => 'Busi NGK',
            'category_id' => $spareparts->id,
            'stock' => 50,
            'purchase_price' => 25000,
            'selling_price' => 35000,
            'description' => 'Busi NGK original'
        ]);

        Product::create([
            'name' => 'Oli AHM 10W-30',
            'category_id' => $oli->id,
            'stock' => 30,
            'purchase_price' => 45000,
            'selling_price' => 65000,
            'description' => 'Oli mesin AHM SPX-2'
        ]);

        Product::create([
            'name' => 'Rantai Motor',
            'category_id' => $spareparts->id,
            'stock' => 20,
            'purchase_price' => 75000,
            'selling_price' => 100000,
            'description' => 'Rantai motor original'
        ]);

        Product::create([
            'name' => 'Helm Half Face',
            'category_id' => $aksesoris->id,
            'stock' => 10,
            'purchase_price' => 200000,
            'selling_price' => 300000,
            'description' => 'Helm half face SNI'
        ]);
    }
}
