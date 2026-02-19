<?php

namespace Tests\Unit\Models;

use App\Models\Student;
use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a student
     */
    public function test_criar_student(): void
    {
        $student = Student::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    /**
     * Test student has correct attributes
     */
    public function test_student_has_correct_attributes(): void
    {
        $student = Student::factory()->create();

        $this->assertNotNull($student->id);
        $this->assertNotNull($student->name);
        $this->assertNotNull($student->email);
        $this->assertNotNull($student->date_of_birth);
    }

    /**
     * Test student has unique email
     */
    public function test_student_email_is_unique(): void
    {
        $student = Student::factory()->create(['email' => 'unique@example.com']);

        // Try to create another with same email - should fail
        $this->expectException(\Exception::class);
        Student::factory()->create(['email' => 'unique@example.com']);
    }

    /**
     * Test student has many registrations
     */
    public function test_student_has_many_registrations(): void
    {
        $student = Student::factory()->create();
        $cursos = Curso::factory(3)->create();

        foreach ($cursos as $curso) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $this->assertEquals(3, $student->registrations()->count());
    }

    /**
     * Test student has many-to-many relationship with cursos
     */
    public function test_student_has_many_cursos(): void
    {
        $student = Student::factory()->create();
        $cursos = Curso::factory(5)->create();

        foreach ($cursos as $curso) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $this->assertEquals(5, $student->cursos()->count());
    }

    /**
     * Test student can be updated
     */
    public function test_update_student(): void
    {
        $student = Student::factory()->create(['name' => 'Maria']);

        $student->update(['name' => 'Maria Silva']);

        $this->assertEquals('Maria Silva', $student->fresh()->name);
    }

    /**
     * Test student can be deleted
     */
    public function test_delete_student(): void
    {
        $student = Student::factory()->create();
        $studentId = $student->id;

        $student->delete();

        $this->assertDatabaseMissing('students', ['id' => $studentId]);
    }

    /**
     * Test deleting student removes registrations
     */
    public function test_delete_student_removes_registrations(): void
    {
        $student = Student::factory()->create();
        $curso = Curso::factory()->create();

        Registration::factory()->create([
            'students_id' => $student->id,
            'cursos_id' => $curso->id,
        ]);

        $student->delete();

        $this->assertDatabaseMissing('registrations', [
            'students_id' => $student->id,
        ]);
    }

    /**
     * Test student date of birth is a date
     */
    public function test_student_date_of_birth_is_date(): void
    {
        $student = Student::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $student->date_of_birth);
    }

    /**
     * Test student fillable attributes
     */
    public function test_student_fillable_attributes(): void
    {
        $data = [
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'date_of_birth' => '1995-05-15',
            'phone' => '11999999999',
            'address' => 'Rua Teste, 123',
        ];

        $student = Student::create($data);

        foreach ($data as $key => $value) {
            if ($key === 'date_of_birth') {
                $this->assertEquals($value, $student->$key->toDateString());
            } else {
                $this->assertEquals($value, $student->$key);
            }
        }
    }

    /**
     * Test student can have multiple registrations in same curso
     */
    public function test_student_multiple_registrations(): void
    {
        $student = Student::factory()->create();
        $cursos = Curso::factory(10)->create();

        foreach ($cursos as $curso) {
            Registration::factory()->create([
                'students_id' => $student->id,
                'cursos_id' => $curso->id,
            ]);
        }

        $this->assertEquals(10, $student->registrations()->count());
    }
}
