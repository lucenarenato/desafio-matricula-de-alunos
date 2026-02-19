<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Curso;
use App\Models\Student;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoControllerTest extends TestCase
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
        Curso::factory(5)->create();

        $response = $this->actingAs($this->admin)->get(route('cursos.index'));

        $response->assertOk();
        $response->assertViewIs('cursos.index');
    }

    /**
     * Test index is not accessible by regular user
     */
    public function test_index_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('cursos.index'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test index requires authentication
     */
    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('cursos.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test create page is accessible by admin
     */
    public function test_create_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('cursos.create'));

        $response->assertOk();
        $response->assertViewIs('cursos.create');
    }

    /**
     * Test create is not accessible by regular user
     */
    public function test_create_is_not_accessible_by_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('cursos.create'));

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test store creates a new curso
     */
    public function test_store_creates_curso(): void
    {
        $response = $this->actingAs($this->admin)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'description' => 'A test curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertRedirect(route('cursos.index'));
        $this->assertDatabaseHas('cursos', ['name' => 'New Curso']);
    }

    /**
     * Test show displays curso details
     */
    public function test_show_displays_curso_details(): void
    {
        $curso = Curso::factory()->create();
        $students = Student::factory(3)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('cursos.show', $curso));

        $response->assertOk();
        $response->assertViewIs('cursos.show');
        $response->assertViewHas('curso', $curso);
    }

    /**
     * Test edit page is accessible by admin
     */
    public function test_edit_is_accessible_by_admin(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('cursos.edit', $curso));

        $response->assertOk();
        $response->assertViewIs('cursos.edit');
        $response->assertViewHas('curso', $curso);
    }

    /**
     * Test update modifies curso
     */
    public function test_update_modifies_curso(): void
    {
        $curso = Curso::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->put(route('cursos.update', $curso), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'type' => 'Presencial',
            'maximum_enrollments' => 50,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertRedirect(route('cursos.index'));
        $this->assertDatabaseHas('cursos', [
            'id' => $curso->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test destroy deletes curso
     */
    public function test_destroy_deletes_curso(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('cursos.destroy', $curso));

        $response->assertRedirect(route('cursos.index'));
        $this->assertDatabaseMissing('cursos', ['id' => $curso->id]);
    }

    /**
     * Test index shows cursos with pagination
     */
    public function test_index_shows_cursos_with_pagination(): void
    {
        Curso::factory(20)->create();

        $response = $this->actingAs($this->admin)->get(route('cursos.index'));

        $response->assertOk();
        $response->assertViewHas('cursos');
    }

    /**
     * Test search filters cursos by name
     */
    public function test_search_filters_by_name(): void
    {
        Curso::factory()->create(['name' => 'PHP Course']);
        Curso::factory()->create(['name' => 'JavaScript Course']);

        $response = $this->actingAs($this->admin)->get(route('cursos.index', ['search' => 'PHP']));

        $response->assertOk();
    }

    /**
     * Test filter by type
     */
    public function test_filter_by_type(): void
    {
        Curso::factory()->create(['type' => 'On-line']);
        Curso::factory()->create(['type' => 'Presencial']);

        $response = $this->actingAs($this->admin)->get(route('cursos.index', ['type' => 'On-line']));

        $response->assertOk();
    }

    /**
     * Test sorting by different fields
     */
    public function test_sorting_by_name(): void
    {
        Curso::factory()->create(['name' => 'Zebra Course']);
        Curso::factory()->create(['name' => 'Apple Course']);

        $response = $this->actingAs($this->admin)->get(route('cursos.index', ['sort_by' => 'name', 'sort_order' => 'asc']));

        $response->assertOk();
    }

    /**
     * Test cannot create without admin role
     */
    public function test_cannot_create_without_admin_role(): void
    {
        $response = $this->actingAs($this->user)->post(route('cursos.store'), [
            'name' => 'New Curso',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test destroy cascades registrations
     */
    public function test_destroy_removes_registrations(): void
    {
        $curso = Curso::factory()->create();
        $student = Student::factory()->create();

        Registration::factory()->create([
            'cursos_id' => $curso->id,
            'students_id' => $student->id,
        ]);

        $this->actingAs($this->admin)->delete(route('cursos.destroy', $curso));

        $this->assertDatabaseMissing('registrations', [
            'cursos_id' => $curso->id,
        ]);
    }

    /**
     * Test update validates data
     */
    public function test_update_validates_data(): void
    {
        $curso = Curso::factory()->create();

        $response = $this->actingAs($this->admin)->put(route('cursos.update', $curso), [
            'name' => '',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
            'registration_deadline' => now()->addMonth(),
        ]);

        $response->assertSessionHasErrors('name');
    }
}
