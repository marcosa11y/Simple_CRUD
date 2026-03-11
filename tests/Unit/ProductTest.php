<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_can_be_created()
    {
        $product = Product::create([
            'name' => 'Laptop',
            'description' => 'High performance laptop',
            'price' => 999.99,
            'stock' => 10
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Laptop',
            'price' => 999.99,
            'stock' => 10
        ]);

        $this->assertInstanceOf(Product::class, $product);
    }

    /** @test */
    public function product_has_required_attributes()
    {
        $product = Product::create([
            'name' => 'Mouse',
            'description' => 'Wireless mouse',
            'price' => 29.99,
            'stock' => 50
        ]);

        $this->assertEquals('Mouse', $product->name);
        $this->assertEquals('Wireless mouse', $product->description);
        $this->assertEquals(29.99, $product->price);
        $this->assertEquals(50, $product->stock);
    }

    /** @test */
    public function product_can_belong_to_multiple_categories()
    {
        $product = Product::create([
            'name' => 'Monitor',
            'description' => 'Gaming monitor',
            'price' => 299.99,
            'stock' => 5
        ]);

        $category1 = Category::create(['name' => 'Electronics']);
        $category2 = Category::create(['name' => 'Gaming']);

        $product->categories()->attach([$category1->id, $category2->id]);

        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($category1));
        $this->assertTrue($product->categories->contains($category2));
    }

    /** @test */
    public function product_can_be_updated()
    {
        $product = Product::create([
            'name' => 'Keyboard',
            'description' => 'Mechanical keyboard',
            'price' => 149.99,
            'stock' => 25
        ]);

        $product->update([
            'price' => 129.99,
            'stock' => 20
        ]);

        $this->assertEquals(129.99, $product->fresh()->price);
        $this->assertEquals(20, $product->fresh()->stock);
    }

    /** @test */
    public function product_can_be_deleted()
    {
        $product = Product::create([
            'name' => 'Headphones',
            'description' => 'Noise-cancelling headphones',
            'price' => 249.99,
            'stock' => 15
        ]);

        $productId = $product->id;
        $product->delete();

        $this->assertNull(Product::find($productId));
    }

    /** @test */
    public function product_fillable_attributes_are_correct()
    {
        $fillable = Product::make()->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('stock', $fillable);
    }

    /** @test */
    public function product_categories_relationship_works()
    {
        $product = Product::create([
            'name' => 'Tablet',
            'description' => 'iPad Pro',
            'price' => 1099.99,
            'stock' => 8
        ]);

        $category = Category::create(['name' => 'Tablets']);
        $product->categories()->attach($category);

        $this->assertTrue($product->categories->contains('id', $category->id));
    }

    /** @test */
    public function multiple_products_can_be_retrieved()
    {
        Product::create(['name' => 'Product 1', 'price' => 10, 'stock' => 100]);
        Product::create(['name' => 'Product 2', 'price' => 20, 'stock' => 50]);
        Product::create(['name' => 'Product 3', 'price' => 30, 'stock' => 25]);

        $products = Product::all();

        $this->assertCount(3, $products);
    }
}
