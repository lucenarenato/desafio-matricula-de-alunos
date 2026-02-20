<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Enums\CursoTypes;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CursoRealSeeder extends Seeder
{
    public function run(): void
    {
        // Array com cursos reais
        $cursos = [
            [
                'name' => 'Desenvolvimento Web Full Stack',
                'description' => 'Aprenda HTML, CSS, JavaScript, PHP, Laravel e muito mais. Curso completo para se tornar um desenvolvedor web full stack.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 50,
                'registration_deadline' => now()->addMonths(2),
            ],
            [
                'name' => 'Introdução à Inteligência Artificial',
                'description' => 'Fundamentos de IA, machine learning e redes neurais. Projetos práticos com Python.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 100,
                'registration_deadline' => now()->addMonths(1),
            ],
            [
                'name' => 'Marketing Digital Avançado',
                'description' => 'Estratégias de SEO, Google Ads, Facebook Ads e marketing de conteúdo para alavancar seus negócios.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 40,
                'registration_deadline' => now()->addMonths(3),
            ],
            [
                'name' => 'Workshop de Fotografia Profissional',
                'description' => 'Aprenda técnicas avançadas de fotografia, edição e composição. Aulas práticas com equipamento profissional.',
                'type' => CursoTypes::InPerson->value,
                'maximum_enrollments' => 20,
                'registration_deadline' => now()->addWeeks(3),
            ],
            [
                'name' => 'Python para Análise de Dados',
                'description' => 'Domine Pandas, NumPy, Matplotlib e outras bibliotecas essenciais para análise de dados com Python.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 60,
                'registration_deadline' => now()->addMonths(2),
            ],
            [
                'name' => 'Inglês para Negócios',
                'description' => 'Curso intensivo de inglês focado em comunicação empresarial, apresentações e negociações.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 30,
                'registration_deadline' => now()->addWeeks(5),
            ],
            [
                'name' => 'Design UX/UI Completo',
                'description' => 'Aprenda a criar experiências digitais incríveis com Figma, prototipagem e testes de usabilidade.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 45,
                'registration_deadline' => now()->addMonths(2),
            ],
            [
                'name' => 'Oficina de Escrita Criativa',
                'description' => 'Desenvolva suas habilidades de escrita com técnicas narrativas, desenvolvimento de personagens e worldbuilding.',
                'type' => CursoTypes::InPerson->value,
                'maximum_enrollments' => 15,
                'registration_deadline' => now()->addWeeks(2),
            ],
            [
                'name' => 'Gestão de Projetos com Metodologias Ágeis',
                'description' => 'Scrum, Kanban, Lean e outras metodologias ágeis aplicadas à gestão de projetos.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 35,
                'registration_deadline' => now()->addMonths(1),
            ],
            [
                'name' => 'Excel Avançado e VBA',
                'description' => 'Domine funções avançadas, tabelas dinâmicas, macros e automação com VBA.',
                'type' => CursoTypes::Online->value,
                'maximum_enrollments' => 40,
                'registration_deadline' => now()->addWeeks(4),
            ],
        ];

        // Criar os cursos
        foreach ($cursos as $curso) {
            Curso::create($curso);
        }

        $this->command->info('✅ Cursos reais criados com sucesso!');
    }
}
