<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuários admin e user (apenas se não existirem)
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        if (!User::where('email', 'user@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
        }

        // Criar cursos
        $cursos = Curso::factory(10)->create();

        // Criar alunos
        $students = Student::factory(30)->create();

        // Criar inscrições (registros)
        // Cada estudante se matricula em 1-3 cursos aleatoriamente
        foreach ($students as $student) {
            $cursos_para_matricula = $cursos->random(rand(1, 3));
            foreach ($cursos_para_matricula as $curso) {
                Registration::factory()->create([
                    'students_id' => $student->id,
                    'cursos_id' => $curso->id,
                ]);
            }
        }
    }
}
