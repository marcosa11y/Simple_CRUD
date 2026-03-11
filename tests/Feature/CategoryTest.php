<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_category()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/categories', [
            'name' => 'Electronics'
        ]);

        $response->assertStatus(201)
                 ->assertJson([ 'name' => 'Electronics' ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    }
}
