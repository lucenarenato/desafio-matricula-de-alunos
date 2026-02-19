<?php

namespace Tests\Unit\Models;

use App\Models\Registration;
use App\Models\Curso;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a registration
     */
    public function test_criar_registration(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        $registration = Registration::factory()->create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);
    }

    /**
     * Test registration has correct attributes
     */
    public function test_registration_has_correct_attributes(): void
    {
        $registration = Registration::factory()->create();

        $this->assertNotNull($registration->id);
        $this->assertNotNull($registration->students_id);
        $this->assertNotNull($registration->cursos_id);
        $this->assertNotNull($registration->created_at);
    }

    /**
     * Test registration belongs to student
     */
    public function test_registration_belongs_to_student(): void
    {
        $student = Student::factory()->create();
        $registration = Registration::factory()->create([
            'students_id' => $student->id,
        ]);

        $this->assertEquals($student->id, $registration->student->id);
    }

    /**
     * Test registration belongs to curso
     */
    public function test_registration_belongs_to_curso(): void
    {
        $curso = Curso::factory()->create();
        $registration = Registration::factory()->create([
            'cursos_id' => $curso->id,
        ]);

        $this->assertEquals($curso->id, $registration->curso->id);
    }

    /**
     * Test registration can be deleted
     */
    public function test_delete_registration(): void
    {
        $registration = Registration::factory()->create();
        $registrationId = $registration->id;

        $registration->delete();

        $this->assertDatabaseMissing('registrations', ['id' => $registrationId]);
    }

    /**
     * Test registration student relationship loads correctly
     */
    public function test_registration_with_student_and_curso(): void
    {
        $student = Student::factory()->create(['name' => 'Test Student']);
        $curso = Curso::factory()->create(['name' => 'Test Curso']);

        $registration = Registration::factory()->create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $registration->load('student', 'curso');

        $this->assertEquals('Test Student', $registration->student->name);
        $this->assertEquals('Test Curso', $registration->curso->name);
    }

    /**
     * Test multiple registrations for same student
     */
    public function test_multiple_registrations_same_student(): void
    {
        $student = Student::factory()->create();
        $cursos = Curso::factory(5)->create();

        foreach ($cursos as $curso) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $registrations = Registration::where('students_id', $student->id)->get();

        $this->assertEquals(5, $registrations->count());
    }

    /**
     * Test multiple registrations for same curso
     */
    public function test_multiple_registrations_same_curso(): void
    {
        $curso = Curso::factory()->create();
        $students = Student::factory(5)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $registrations = Registration::where('cursos_id', $curso->id)->get();

        $this->assertEquals(5, $registrations->count());
    }

    /**
     * Test registration fillable attributes
     */
    public function test_registration_fillable_attributes(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        $registration = Registration::create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $this->assertEquals($student->id, $registration->students_id);
        $this->assertEquals($curso->id, $registration->cursos_id);
    }
}
