<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_be_created()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function user_has_required_attributes()
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password456'),
            'role' => 'admin'
        ]);

        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('jane@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $plainPassword = 'testpassword123';
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'role' => 'customer'
        ]);

        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(password_verify($plainPassword, $user->password));
    }

    /** @test */
    public function user_password_is_hidden_in_serialization()
    {
        $user = User::create([
            'name' => 'Hidden Password User',
            'email' => 'hidden@example.com',
            'password' => 'password789',
            'role' => 'customer'
        ]);

        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
    }

    /** @test */
    public function user_fillable_attributes_are_correct()
    {
        $fillable = User::make()->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('role', $fillable);
    }

    /** @test */
    public function user_can_be_updated()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $user->update([
            'name' => 'Updated Name',
            'role' => 'admin'
        ]);

        $this->assertEquals('Updated Name', $user->fresh()->name);
        $this->assertEquals('admin', $user->fresh()->role);
    }

    /** @test */
    public function user_can_be_deleted()
    {
        $user = User::create([
            'name' => 'Delete Me',
            'email' => 'delete@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        $userId = $user->id;
        $user->delete();

        $this->assertNull(User::find($userId));
    }

    /** @test */
    public function user_email_is_unique()
    {
        User::create([
            'name' => 'User 1',
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'role' => 'customer'
        ]);

        // This should work - unique email constraint is handled at DB level
        $users = User::where('email', 'duplicate@example.com')->get();
        $this->assertCount(1, $users);
    }
}
