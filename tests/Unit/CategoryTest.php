<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function category_can_be_created()
    {
        $category = Category::create(['name' => 'Electronics']);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);

        $this->assertInstanceOf(Category::class, $category);
    }

    /** @test */
    public function category_has_name_attribute()
    {
        $category = Category::create(['name' => 'Clothing']);

        $this->assertEquals('Clothing', $category->name);
    }

    /** @test */
    public function category_can_have_multiple_products()
    {
        $category = Category::create(['name' => 'Books']);

        $product1 = Product::create([
            'name' => 'Book 1',
            'price' => 19.99,
            'stock' => 100
        ]);

        $product2 = Product::create([
            'name' => 'Book 2',
            'price' => 24.99,
            'stock' => 75
        ]);

        $category->products()->attach([$product1->id, $product2->id]);

        $this->assertCount(2, $category->products);
        $this->assertTrue($category->products->contains($product1));
        $this->assertTrue($category->products->contains($product2));
    }

    /** @test */
    public function category_can_be_updated()
    {
        $category = Category::create(['name' => 'Sports']);

        $category->update(['name' => 'Sports & Outdoors']);

        $this->assertEquals('Sports & Outdoors', $category->fresh()->name);
    }

    /** @test */
    public function category_can_be_deleted()
    {
        $category = Category::create(['name' => 'Home']);

        $categoryId = $category->id;
        $category->delete();

        $this->assertNull(Category::find($categoryId));
    }

    /** @test */
    public function category_fillable_attributes_are_correct()
    {
        $fillable = Category::make()->getFillable();

        $this->assertContains('name', $fillable);
    }

    /** @test */
    public function category_products_relationship_works()
    {
        $category = Category::create(['name' => 'Fashion']);

        $product = Product::create([
            'name' => 'T-Shirt',
            'price' => 29.99,
            'stock' => 200
        ]);

        $category->products()->attach($product);

        $this->assertTrue($category->products->contains('id', $product->id));
    }

    /** @test */
    public function multiple_categories_can_be_retrieved()
    {
        Category::create(['name' => 'Category 1']);
        Category::create(['name' => 'Category 2']);
        Category::create(['name' => 'Category 3']);

        $categories = Category::all();

        $this->assertCount(3, $categories);
    }
}
