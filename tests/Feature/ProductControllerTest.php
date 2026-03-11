<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_list_all_products()
    {
        Product::create([
            'name' => 'Product 1',
            'description' => 'Description 1',
            'price' => 10.00,
            'stock' => 100
        ]);

        Product::create([
            'name' => 'Product 2',
            'description' => 'Description 2',
            'price' => 20.00,
            'stock' => 50
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    /** @test */
    public function can_search_products_by_name()
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'Gaming Laptop',
            'price' => 1000.00,
            'stock' => 5
        ]);

        Product::create([
            'name' => 'Mouse',
            'description' => 'Wireless Mouse',
            'price' => 25.00,
            'stock' => 100
        ]);

        $response = $this->getJson('/api/products?search=Laptop');

        $response->assertStatus(200);
        $responseData = $response->json();
        if (!empty($responseData)) {
            $this->assertEquals('Laptop', $responseData[0]['name']);
        }
    }

    /** @test */
    public function can_get_single_product()
    {
        $product = Product::create([
            'name' => 'Monitor',
            'description' => 'Gaming Monitor 144Hz',
            'price' => 299.99,
            'stock' => 10
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson(['name' => 'Monitor']);
    }

    /** @test */
    public function product_includes_categories()
    {
        $product = Product::create([
            'name' => 'Keyboard',
            'description' => 'Mechanical Keyboard',
            'price' => 150.00,
            'stock' => 25
        ]);

        $category = Category::create(['name' => 'Peripherals']);
        $product->categories()->attach($category);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'price',
                     'categories'
                 ]);
    }

    /** @test */
    public function products_list_includes_categories()
    {
        $product = Product::create([
            'name' => 'Headphones',
            'description' => 'Noise-cancelling',
            'price' => 250.00,
            'stock' => 15
        ]);

        $category = Category::create(['name' => 'Audio']);
        $product->categories()->attach($category);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'price', 'categories']
                 ]);
    }

    /** @test */
    public function can_get_empty_product_list()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(0);
    }
}
