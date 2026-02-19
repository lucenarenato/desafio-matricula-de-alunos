<?php

namespace Tests\Unit\Models;

use App\Models\Curso;
use App\Models\Student;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a curso
     */
    public function test_criar_curso(): void
    {
        $curso = Curso::factory()->create([
            'name' => 'PHP Avançado',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
        ]);

        $this->assertDatabaseHas('cursos', [
            'id' => $curso->id,
            'name' => 'PHP Avançado',
            'type' => 'On-line',
            'maximum_enrollments' => 30,
        ]);
    }

    /**
     * Test that a curso has the correct attributes
     */
    public function test_curso_has_correct_attributes(): void
    {
        $curso = Curso::factory()->create();

        $this->assertNotNull($curso->id);
        $this->assertNotNull($curso->name);
        $this->assertNotNull($curso->type);
        $this->assertNotNull($curso->maximum_enrollments);
        $this->assertNotNull($curso->registration_deadline);
    }

    /**
     * Test curso has registrations relationship
     */
    public function test_curso_has_many_registrations(): void
    {
        $curso = Curso::factory()->create();
        $students = Student::factory(3)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $this->assertEquals(3, $curso->registrations()->count());
    }

    /**
     * Test curso has many-to-many relationship with students
     */
    public function test_curso_has_many_students(): void
    {
        $curso = Curso::factory()->create();
        $students = Student::factory(5)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $this->assertEquals(5, $curso->students()->count());
    }

    /**
     * Test get enrolled count
     */
    public function test_get_enrolled_count(): void
    {
        $curso = Curso::factory()->create(['maximum_enrollments' => 30]);
        $students = Student::factory(10)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $this->assertEquals(10, $curso->getEnrolledCountAttribute());
    }

    /**
     * Test get available spots
     */
    public function test_get_available_spots(): void
    {
        $curso = Curso::factory()->create(['maximum_enrollments' => 30]);
        $students = Student::factory(10)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $this->assertEquals(20, $curso->getAvailableSpotsAttribute());
    }

    /**
     * Test is full attribute
     */
    public function test_is_full_attribute(): void
    {
        $curso = Curso::factory()->create(['maximum_enrollments' => 5]);
        $students = Student::factory(5)->create();

        foreach ($students as $student) {
            Registration::factory()->create([
                'cursos_id' => $curso->id,
                'students_id' => $student->id,
            ]);
        }

        $this->assertTrue($curso->isFullAttribute());
    }

    /**
     * Test is registration open attribute
     */
    public function test_is_registration_open_attribute(): void
    {
        $futureDate = now()->addDays(10);
        $curso = Curso::factory()->create([
            'registration_deadline' => $futureDate,
        ]);

        $this->assertTrue($curso->isRegistrationOpenAttribute());
    }

    /**
     * Test is registration closed when deadline passed
     */
    public function test_is_registration_closed_when_deadline_passed(): void
    {
        $pastDate = now()->subDays(10);
        $curso = Curso::factory()->create([
            'registration_deadline' => $pastDate,
        ]);

        $this->assertFalse($curso->isRegistrationOpenAttribute());
    }

    /**
     * Test curso can be updated
     */
    public function test_update_curso(): void
    {
        $curso = Curso::factory()->create(['name' => 'Curso Original']);

        $curso->update(['name' => 'Curso Atualizado']);

        $this->assertEquals('Curso Atualizado', $curso->fresh()->name);
    }

    /**
     * Test curso can be deleted
     */
    public function test_delete_curso(): void
    {
        $curso = Curso::factory()->create();
        $cursoId = $curso->id;

        $curso->delete();

        $this->assertDatabaseMissing('cursos', ['id' => $cursoId]);
    }

    /**
     * Test deleting curso removes registrations
     */
    public function test_delete_curso_removes_registrations(): void
    {
        $curso = Curso::factory()->create();
        $student = Student::factory()->create();

        Registration::factory()->create([
            'cursos_id' => $curso->id,
            'students_id' => $student->id,
        ]);

        $curso->delete();

        $this->assertDatabaseMissing('registrations', [
            'cursos_id' => $curso->id,
        ]);
    }

    /**
     * Test fillable attributes
     */
    public function test_curso_fillable_attributes(): void
    {
        $data = [
            'name' => 'Test Curso',
            'description' => 'Test Description',
            'type' => 'On-line',
            'maximum_enrollments' => 50,
            'registration_deadline' => now()->addMonth(),
        ];

        $curso = Curso::create($data);

        foreach ($data as $key => $value) {
            if ($key === 'registration_deadline') {
                $this->assertEquals($value->toDateString(), $curso->$key->toDateString());
            } else {
                $this->assertEquals($value, $curso->$key);
            }
        }
    }
}
