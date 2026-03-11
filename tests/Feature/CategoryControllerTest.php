<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    public function setUp(): void
    {
        parent::setUp();
        // create an admin user for protected operations
        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function can_list_all_categories()
    {
        Category::create(['name' => 'Electronics']);
        Category::create(['name' => 'Clothing']);
        Category::create(['name' => 'Books']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function can_get_empty_category_list()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(0);
    }

    /** @test */
    public function can_create_category()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => 'Electronics'
        ]);

        $response->assertStatus(201)
                 ->assertJson(['name' => 'Electronics']);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    }

    /** @test */
    public function category_name_is_required()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => ''
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function category_name_must_be_string()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => 12345
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function category_name_max_length_is_255()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $longName = str_repeat('A', 256);

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => $longName
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function can_update_category()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $category = Category::create(['name' => 'Old Name']);

        $response = $this->withHeader('Authorization', "Bearer $token")->putJson("/api/categories/{$category->id}", [
            'name' => 'New Name'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['name' => 'New Name']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name'
        ]);
    }

    /** @test */
    public function can_delete_category()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $category = Category::create(['name' => 'To Delete']);

        $response = $this->withHeader('Authorization', "Bearer $token")->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Category deleted']);

        $this->assertNull(Category::find($category->id));
    }

    /** @test */
    public function category_includes_products()
    {
        $category = Category::create(['name' => 'Phones']);

        $product1 = Product::create([
            'name' => 'iPhone 14',
            'price' => 999.99,
            'stock' => 20
        ]);

        $product2 = Product::create([
            'name' => 'Samsung Galaxy',
            'price' => 899.99,
            'stock' => 15
        ]);

        $category->products()->attach([$product1->id, $product2->id]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'products']
                 ]);
    }

    /** @test */
    public function can_get_single_category()
    {
        $category = Category::create(['name' => 'Laptops']);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson(['name' => 'Laptops']);
    }

    /** @test */
    public function create_category_returns_id()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => 'New Category'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'name', 'created_at', 'updated_at']);
    }

    /** @test */
    public function update_requires_valid_category_id()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->putJson('/api/categories/9999', [
            'name' => 'Non-existent'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function delete_requires_valid_category_id()
    {
        $token = $this->admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")->deleteJson('/api/categories/9999');

        $response->assertStatus(404);
    }

    /** @test */
    public function non_admin_cannot_create_category()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => 'Hacker Category'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', ['name' => 'Hacker Category']);
    }

    /** @test */
    public function non_admin_cannot_update_category()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
        $token = $user->createToken('test')->plainTextToken;
        $category = Category::create(['name' => 'Original']);

        $response = $this->withHeader('Authorization', "Bearer $token")->putJson("/api/categories/{$category->id}", [
            'name' => 'Hacked'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Original']);
    }

    /** @test */
    public function non_admin_cannot_delete_category()
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);
        $token = $user->createToken('test')->plainTextToken;
        $category = Category::create(['name' => 'Protected']);

        $response = $this->withHeader('Authorization', "Bearer $token")->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
        $this->assertNotNull(Category::find($category->id));
    }
}
