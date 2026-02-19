<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * Test name is required
     */
    public function test_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => '',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test email is required
     */
    public function test_email_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => '',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test email must be valid
     */
    public function test_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'invalid-email',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test email must be unique
     */
    public function test_email_must_be_unique(): void
    {
        Student::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'New Student',
            'email' => 'existing@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test date of birth is required
     */
    public function test_date_of_birth_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '',
        ]);

        $response->assertSessionHasErrors('date_of_birth');
    }

    /**
     * Test date of birth must be in the past
     */
    public function test_date_of_birth_must_be_in_past(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => now()->addDays(1)->toDateString(),
        ]);

        $response->assertSessionHasErrors('date_of_birth');
    }

    /**
     * Test valid student creation
     */
    public function test_valid_student_creation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'date_of_birth' => '1995-05-15',
            'phone' => '11999999999',
            'address' => 'Rua Teste, 123',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    /**
     * Test phone is optional
     */
    public function test_phone_is_optional(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test address is optional
     */
    public function test_address_is_optional(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertRedirect(route('students.index'));
    }

    /**
     * Test phone must not exceed 20 characters
     */
    public function test_phone_must_not_exceed_20_chars(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
            'phone' => str_repeat('1', 21),
        ]);

        $response->assertSessionHasErrors('phone');
    }

    /**
     * Test address must not exceed 255 characters
     */
    public function test_address_must_not_exceed_255_chars(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
            'address' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors('address');
    }

    /**
     * Test name must not exceed 255 characters
     */
    public function test_name_must_not_exceed_255_chars(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
