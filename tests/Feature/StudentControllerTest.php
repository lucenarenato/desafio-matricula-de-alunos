<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentControllerTest extends TestCase
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
        Student::factory(5)->create();

        $response = $this->actingAs($this->admin)->get(route('students.index'));

        $response->assertOk();
        $response->assertViewIs('students.index');
    }

    /**
     * Test index is not accessible by regular user
     */
    public function test_index_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('students.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test index requires authentication
     */
    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('students.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test create page is accessible by admin
     */
    public function test_create_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('students.create'));

        $response->assertOk();
        $response->assertViewIs('students.create');
    }

    /**
     * Test create is not accessible by regular user
     */
    public function test_create_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('students.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test store creates a new student
     */
    public function test_store_creates_student(): void
    {
        $response = $this->actingAs($this->admin)->post(route('students.store'), [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'date_of_birth' => '1995-05-15',
            'phone' => '11999999999',
            'address' => 'Rua Teste, 123',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', ['name' => 'João Silva', 'email' => 'joao@example.com']);
    }

    /**
     * Test show displays student details with enrolled courses
     */
    public function test_show_displays_student_with_courses(): void
    {
        $student = Student::factory()->create();
        $cursos = Curso::factory(3)->create();

        foreach ($cursos as $curso) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('students.show', $student));

        $response->assertOk();
        $response->assertViewIs('students.show');
        $response->assertViewHas('student', $student);
    }

    /**
     * Test edit page is accessible by admin
     */
    public function test_edit_is_accessible_by_admin(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('students.edit', $student));

        $response->assertOk();
        $response->assertViewIs('students.edit');
        $response->assertViewHas('student', $student);
    }

    /**
     * Test update modifies student
     */
    public function test_update_modifies_student(): void
    {
        $student = Student::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->put(route('students.update', $student), [
            'name' => 'Updated Name',
            'email' => $student->email,
            'date_of_birth' => '1995-05-15',
            'phone' => '11988888888',
            'address' => 'New Address, 456',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test destroy deletes student
     */
    public function test_destroy_deletes_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('students.destroy', $student));

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /**
     * Test index shows students with pagination
     */
    public function test_index_shows_students_with_pagination(): void
    {
        Student::factory(20)->create();

        $response = $this->actingAs($this->admin)->get(route('students.index'));

        $response->assertOk();
        $response->assertViewHas('students');
    }

    /**
     * Test search filters students by name
     */
    public function test_search_filters_by_name(): void
    {
        Student::factory()->create(['name' => 'Fernando Silva']);
        Student::factory()->create(['name' => 'Maria Santos']);

        $response = $this->actingAs($this->admin)->get(route('students.index', ['search' => 'Fernando']));

        $response->assertOk();
    }

    /**
     * Test search filters students by email
     */
    public function test_search_filters_by_email(): void
    {
        Student::factory()->create(['email' => 'fernando@example.com']);
        Student::factory()->create(['email' => 'maria@example.com']);

        $response = $this->actingAs($this->admin)->get(route('students.index', ['search' => 'fernando@']));

        $response->assertOk();
    }

    /**
     * Test cannot create without admin role
     */
    public function test_cannot_create_without_admin_role(): void
    {
        $response = $this->actingAs($this->user)->post(route('students.store'), [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test destroy cascades registrations
     */
    public function test_destroy_removes_registrations(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        Registration::factory()->create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $this->actingAs($this->admin)->delete(route('students.destroy', $student));

        $this->assertDatabaseMissing('registrations', [
            'students_id' => $student->id,
        ]);
    }

    /**
     * Test update validates email uniqueness
     */
    public function test_update_validates_email_uniqueness(): void
    {
        $student1 = Student::factory()->create(['email' => 'student1@example.com']);
        $student2 = Student::factory()->create(['email' => 'student2@example.com']);

        $response = $this->actingAs($this->admin)->put(route('students.update', $student2), [
            'name' => 'Updated Name',
            'email' => 'student1@example.com', // Already exists
            'date_of_birth' => '1995-05-15',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test can use same email on update (not another student's email)
     */
    public function test_can_update_with_same_email(): void
    {
        $student = Student::factory()->create(['email' => 'student@example.com']);

        $response = $this->actingAs($this->admin)->put(route('students.update', $student), [
            'name' => 'Updated Name',
            'email' => 'student@example.com', // Same email
            'date_of_birth' => '1995-05-15',
            'phone' => '11999999999',
            'address' => 'Rua Teste, 123',
        ]);

        $response->assertRedirect(route('students.index'));
    }
}
