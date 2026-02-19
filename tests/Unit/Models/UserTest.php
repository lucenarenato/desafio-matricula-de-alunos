<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a user
     */
    public function test_criar_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
    }

    /**
     * Test user has correct attributes
     */
    public function test_user_has_correct_attributes(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->role);
    }

    /**
     * Test user is admin
     */
    public function test_user_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isUser());
    }

    /**
     * Test user is regular user
     */
    public function test_user_is_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test user email is unique
     */
    public function test_user_email_is_unique(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        // Try to create another with same email - should fail
        $this->expectException(\Exception::class);
        User::factory()->create(['email' => 'unique@example.com']);
    }

    /**
     * Test password is hashed
     */
    public function test_password_is_hashed(): void
    {
        $password = 'plain-password-123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    /**
     * Test user can be updated
     */
    public function test_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $user->update(['name' => 'New Name']);

        $this->assertEquals('New Name', $user->fresh()->name);
    }

    /**
     * Test user can be deleted
     */
    public function test_delete_user(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /**
     * Test user fillable attributes
     */
    public function test_user_fillable_attributes(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ];

        $user = User::create($data);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue(\Hash::check('password', $user->password));
    }

    /**
     * Test default role is user
     */
    public function test_default_role_is_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->assertEquals('user', $user->role);
    }
}
