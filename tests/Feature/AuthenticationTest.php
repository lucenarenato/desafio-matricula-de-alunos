<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use App\Models\Student;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /**
     * Test unauthenticated user cannot access cursos index
     */
    public function test_unauthenticated_cannot_access_cursos_index(): void
    {
        $response = $this->get(route('cursos.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test unauthenticated user cannot access students index
     */
    public function test_unauthenticated_cannot_access_students_index(): void
    {
        $response = $this->get(route('students.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test unauthenticated user cannot access registrations index
     */
    public function test_unauthenticated_cannot_access_registrations_index(): void
    {
        $response = $this->get(route('registrations.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test regular user cannot access cursos index
     */
    public function test_user_cannot_access_cursos_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('cursos.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot access students index
     */
    public function test_user_cannot_access_students_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('students.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot access registrations index
     */
    public function test_user_cannot_access_registrations_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('registrations.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot access cursos create
     */
    public function test_user_cannot_access_cursos_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('cursos.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot access students create
     */
    public function test_user_cannot_access_students_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('students.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot access registrations create
     */
    public function test_user_cannot_access_registrations_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('registrations.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot store curso
     */
    public function test_user_cannot_store_curso(): void
    {
        $response = $this->actingAs($this->user)->post(route('cursos.store'), [
            'name' => 'Test Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);


        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('cursos', ['name' => 'Test Curso']);
    }

    /**
     * Test regular user cannot store student
     */
    public function test_user_cannot_store_student(): void
    {
        $response = $this->actingAs($this->user)->post(route('students.store'), [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('students', ['name' => 'Test Student']);
    }

    /**
     * Test regular user cannot store registration
     */
    public function test_user_cannot_store_registration(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->user)->post(route('registrations.store'), [
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test regular user cannot update curso
     */
    public function test_user_cannot_update_curso(): void
    {
        $curso = Curso::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($this->user)->put(route('cursos.update', $curso), [
            'name' => 'Updated Name',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('cursos', ['id' => $curso->id, 'name' => 'Original Name']);
    }

    /**
     * Test regular user cannot delete curso
     */
    public function test_user_cannot_delete_curso(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('cursos.destroy', $curso));

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('cursos', ['id' => $curso->id]);
    }

    /**
     * Test regular user cannot delete student
     */
    public function test_user_cannot_delete_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('students.destroy', $student));

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    /**
     * Test regular user cannot delete registration
     */
    public function test_user_cannot_delete_registration(): void
    {
        $registration = Registration::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('registrations.destroy', $registration));

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('registrations', ['id' => $registration->id]);
    }

    /**
     * Test admin can access all routes
     */
    public function test_admin_can_access_cursos_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('cursos.index'));

        $response->assertOk();
    }

    /**
     * Test admin can access students index
     */
    public function test_admin_can_access_students_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('students.index'));

        $response->assertOk();
    }

    /**
     * Test admin can access registrations index
     */
    public function test_admin_can_access_registrations_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('registrations.index'));

        $response->assertOk();
    }

    /**
     * Test admin can perform all CRUD operations
     */
    public function test_admin_can_perform_crud_operations(): void
    {
        // Create
        $cursoResponse = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'Admin Curso',
            'description' => 'Test',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $cursoResponse->assertRedirect(route('cursos.index'));
        $this->assertDatabaseHas('cursos', ['name' => 'Admin Curso']);

        // Read
        $curso = Curso::where('name', 'Admin Curso')->first();
        $getResponse = $this->actingAs($this->admin)->get(route('cursos.show', $curso));
        $getResponse->assertOk();

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('cursos.update', $curso), [
            'name' => 'Updated Admin Curso',
            'description' => 'Updated',
            'type' => 'Presencial',
            'maximum_enrollments' => 50,
            'registration_deadline' => now()->addMonth(),
        ]);

        $updateResponse->assertRedirect(route('cursos.index'));
        $this->assertDatabaseHas('cursos', ['id' => $curso->id, 'name' => 'Updated Admin Curso']);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('cursos.destroy', $curso));
        $deleteResponse->assertRedirect(route('cursos.index'));
        $this->assertDatabaseMissing('cursos', ['id' => $curso->id]);
    }
}
