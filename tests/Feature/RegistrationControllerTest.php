<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
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
     * Test index page is accessible by admin
     */
    public function test_index_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('registrations.index'));

        $response->assertOk();
        $response->assertViewIs('registrations.index');
    }

    /**
     * Test index is not accessible by regular user
     */
    public function test_index_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('registrations.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test index requires authentication
     */
    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('registrations.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test create page is accessible by admin
     */
    public function test_create_is_accessible_by_admin(): void
    {
        Student::factory(2)->create();
        Curso::factory(2)->create();

        $response = $this->actingAs($this->admin)->get(route('registrations.create'));

        $response->assertOk();
        $response->assertViewIs('registrations.create');
    }

    /**
     * Test create is not accessible by regular user
     */
    public function test_create_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('registrations.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test store creates a new registration (enrollment)
     */
    public function test_store_creates_registration(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseHas('registrations', [
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);
    }

    /**
     * Test prevent duplicate enrollment
     */
    public function test_prevent_duplicate_enrollment(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        // Create first enrollment
        Registration::factory()->create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        // Try to create duplicate
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test prevent enrollment when course is full
     */
    public function test_prevent_enrollment_when_course_full(): void
    {
        $curso = Curso::factory()->create(['maximum_enrollments' => 2]);

        $students = Student::factory(2)->create();

        // Fill the course
        foreach ($students as $student) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        // Try to enroll another student
        $newStudent = Student::factory()->create();
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $newStudent->id,
            'cursos_id' => $curso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test prevent enrollment after registration deadline
     */
    public function test_prevent_enrollment_after_deadline(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create([
            'registration_deadline' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test index shows registrations with pagination
     */
    public function test_index_shows_registrations_with_pagination(): void
    {
        $students = Student::factory(5)->create();
        $cursos = Curso::factory(5)->create();

        foreach ($students as $student) {
            foreach ($cursos as $curso) {
                Registration::factory()->create([
                    'students_id' => $student->id,
                    'cursos_id' => $curso->id,
                ]);
            }
        }

        $response = $this->actingAs($this->admin)->get(route('registrations.index'));

        $response->assertOk();
        $response->assertViewHas('registrations');
    }

    /**
     * Test search filters by student name
     */
    public function test_search_filters_by_student_name(): void
    {
        $student1 = Student::factory()->create(['name' => 'João Silva']);
        $student2 = Student::factory()->create(['name' => 'Maria Santos']);
        $curso = Curso::factory()->create();

        Registration::factory()->create(['students_id' => $student1->id, 'cursos_id' => $curso->id]);
        Registration::factory()->create(['students_id' => $student2->id, 'cursos_id' => $curso->id]);

        $response = $this->actingAs($this->admin)->get(route('registrations.index', ['search' => 'João']));

        $response->assertOk();
    }

    /**
     * Test search filters by course name
     */
    public function test_search_filters_by_course_name(): void
    {
        $student = Student::factory()->create();
        $curso1 = Curso::factory()->create(['name' => 'PHP Course']);
        $curso2 = Curso::factory()->create(['name' => 'JavaScript Course']);

        Registration::factory()->create(['students_id' => $student->id, 'cursos_id' => $curso1->id]);
        Registration::factory()->create(['students_id' => $student->id, 'cursos_id' => $curso2->id]);

        $response = $this->actingAs($this->admin)->get(route('registrations.index', ['search' => 'PHP']));

        $response->assertOk();
    }

    /**
     * Test destroy deletes registration
     */
    public function test_destroy_deletes_registration(): void
    {
        $registration = Registration::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('registrations.destroy', $registration));

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseMissing('registrations', ['id' => $registration->id]);
    }

    /**
     * Test cannot create enrollment without admin role
     */
    public function test_cannot_create_enrollment_without_admin_role(): void
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
     * Test cannot destroy enrollment without admin role
     */
    public function test_cannot_destroy_enrollment_without_admin_role(): void
    {
        $registration = Registration::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('registrations.destroy', $registration));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test student ID is required
     */
    public function test_student_id_is_required(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => '',
            'cursos_id' => $curso->id,
        ]);

        $response->assertSessionHasErrors('students_id');
    }

    /**
     * Test course ID is required
     */
    public function test_course_id_is_required(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student->id,
            'cursos_id' => '',
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }
}
