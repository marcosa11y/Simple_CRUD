<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        User::create([
            'name' => 'Existing',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer'
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Dup',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_login_and_receive_token()
    {
        $user = User::create([
            'name' => 'Login User',
            'email' => 'login@example.com',
            'password' => Hash::make('secret'),
            'role' => 'customer'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'secret'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function login_with_bad_credentials_fails()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'doesnot@exist.com',
            'password' => 'wrong'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function authenticated_user_can_fetch_user_and_logout()
    {
        $user = User::create([
            'name' => 'Fetch User',
            'email' => 'fetch@example.com',
            'password' => Hash::make('secret'),
            'role' => 'customer'
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer $token")->getJson('/api/user')
             ->assertStatus(200)
             ->assertJson(['email' => 'fetch@example.com']);

        $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout')
             ->assertStatus(200);
    }
}
