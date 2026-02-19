<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoValidationTest extends TestCase
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
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => '',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test name must be unique
     */
    public function test_name_must_be_unique(): void
    {
        Curso::factory()->create(['name' => 'Existing Curso']);

        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'Existing Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test type is required
     */
    public function test_type_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => '',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('type');
    }

    /**
     * Test maximum enrollments is required
     */
    public function test_maximum_enrollments_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => '',
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('maximum_enrollments');
    }

    /**
     * Test maximum enrollments must be at least 1
     */
    public function test_maximum_enrollments_must_be_at_least_1(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 0,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('maximum_enrollments');
    }

    /**
     * Test registration deadline is required
     */
    public function test_registration_deadline_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => '',
        ]);

        $response->assertSessionHasErrors('registration_deadline');
    }

    /**
     * Test registration deadline must be in the future
     */
    public function test_registration_deadline_must_be_in_future(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->subDays(1),
        ]);

        $response->assertSessionHasErrors('registration_deadline');
    }

    /**
     * Test valid curso creation
     */
    public function test_valid_curso_creation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'PHP Avançado',
            'description' => 'Um curso de PHP avançado',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertRedirect(route('cursos.index'));
        $this->assertDatabaseHas('cursos', [
            'name' => 'PHP Avançado',
        ]);
    }

    /**
     * Test type must be valid enum
     */
    public function test_type_must_be_valid_enum(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'InvalidType',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('type');
    }

    /**
     * Test name must not exceed 255 characters
     */
    public function test_name_must_not_exceed_255_chars(): void
    {
        $longName = str_repeat('a', 256);

        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => $longName,
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test maximum enrollments must be integer
     */
    public function test_maximum_enrollments_must_be_integer(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 'not_an_integer',
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('maximum_enrollments');
    }
}
