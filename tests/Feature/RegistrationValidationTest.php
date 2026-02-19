<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;
    protected $curso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = Student::factory()->create();
        $this->curso = Curso::factory()->create();
    }

    /**
     * Test student ID is required
     */
    public function test_student_id_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => '',
            'cursos_id' => $this->curso->id,
        ]);

        $response->assertSessionHasErrors('students_id');
    }

    /**
     * Test course ID is required
     */
    public function test_course_id_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => '',
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test student ID must exist
     */
    public function test_student_id_must_exist(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => 9999,
            'cursos_id' => $this->curso->id,
        ]);

        $response->assertSessionHasErrors('students_id');
    }

    /**
     * Test course ID must exist
     */
    public function test_course_id_must_exist(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => 9999,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test cannot enroll student twice in same course
     */
    public function test_cannot_enroll_student_twice(): void
    {
        // First enrollment
        $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $this->curso->id,
        ]);

        // Attempt duplicate enrollment
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $this->curso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test cannot enroll when course is full
     */
    public function test_cannot_enroll_when_course_full(): void
    {
        $fullCurso = Curso::factory()->create(['maximum_enrollments' => 1]);
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        // Fill the course
        $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student1->id,
            'cursos_id' => $fullCurso->id,
        ]);

        // Try to enroll another student
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student2->id,
            'cursos_id' => $fullCurso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test cannot enroll after registration deadline
     */
    public function test_cannot_enroll_after_deadline(): void
    {
        $closedCurso = Curso::factory()->create([
            'registration_deadline' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $closedCurso->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test can enroll before deadline
     */
    public function test_can_enroll_before_deadline(): void
    {
        $openCurso = Curso::factory()->create([
            'registration_deadline' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $openCurso->id,
        ]);

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseHas('registrations', [
            'students_id' => $this->student->id,
            'cursos_id' => $openCurso->id,
        ]);
    }

    /**
     * Test can enroll when vagas are available
     */
    public function test_can_enroll_when_vagas_available(): void
    {
        $cursoWithVagas = Curso::factory()->create(['maximum_enrollments' => 10]);

        // Enroll 5 students
        for ($i = 0; $i < 5; $i++) {
            $student = Student::factory()->create();
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $cursoWithVagas->id,
            ]);
        }

        // Try to enroll another student (should succeed)
        $newStudent = Student::factory()->create();
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $newStudent->id,
            'cursos_id' => $cursoWithVagas->id,
        ]);

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseHas('registrations', [
            'students_id' => $newStudent->id,
            'cursos_id' => $cursoWithVagas->id,
        ]);
    }

    /**
     * Test successful enrollment stores data correctly
     */
    public function test_successful_enrollment_stores_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $this->curso->id,
        ]);

        $response->assertRedirect(route('registrations.index'));
        $this->assertDatabaseHas('registrations', [
            'students_id' => $this->student->id,
            'cursos_id' => $this->curso->id,
        ]);
    }

    /**
     * Test enrollment validates form data
     */
    public function test_enrollment_validates_form_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => 'invalid',
            'cursos_id' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['students_id', 'cursos_id']);
    }

    /**
     * Test course at exact capacity can accept one more
     */
    public function test_course_at_max_capacity_rejects_new_enrollment(): void
    {
        $cursoAtMax = Curso::factory()->create(['maximum_enrollments' => 2]);
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();
        $student3 = Student::factory()->create();

        // Enroll two students (at max)
        Registration::factory()->create([
            'students_id' => $student1->id,
            'cursos_id' => $cursoAtMax->id,
        ]);
        Registration::factory()->create([
            'students_id' => $student2->id,
            'cursos_id' => $cursoAtMax->id,
        ]);

        // Try to enroll third student
        $response = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $student3->id,
            'cursos_id' => $cursoAtMax->id,
        ]);

        $response->assertSessionHasErrors('cursos_id');
    }

    /**
     * Test same student can enroll in different courses
     */
    public function test_student_can_enroll_in_multiple_courses(): void
    {
        $curso1 = Curso::factory()->create();
        $curso2 = Curso::factory()->create();

        // Enroll in first course
        $response1 = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $curso1->id,
        ]);

        $response1->assertRedirect(route('registrations.index'));

        // Enroll in second course
        $response2 = $this->actingAs($this->admin)->post(route('registrations.store'), [
            'students_id' => $this->student->id,
            'cursos_id' => $curso2->id,
        ]);

        $response2->assertRedirect(route('registrations.index'));

        // Both enrollments should exist
        $this->assertDatabaseHas('registrations', [
            'students_id' => $this->student->id,
            'cursos_id' => $curso1->id,
        ]);
        $this->assertDatabaseHas('registrations', [
            'students_id' => $this->student->id,
            'cursos_id' => $curso2->id,
        ]);
    }
}
