# Implementação: Sistema de Matrícula para Alunos

## Resumo das Mudanças

Um sistema completo foi implementado para permitir que usuários comuns (alunos) se matriculem em um ou mais cursos através da interface web.

## Arquivos Modificados

### 1. **Modelos (Models)**

#### `app/Models/User.php`
- ✅ Adicionado relacionamento `registrations()` para vincular usuários às suas matrículas

#### `app/Models/Registration.php`
- ✅ Adicionado relacionamento `user()` para vincular registros aos usuários
- ✅ Adicionado `user_id` ao array `$fillable`

### 2. **Controladores (Controllers)**

#### `app/Http/Controllers/CursoController.php`
- ✅ **`list()`** - Nova ação para listar cursos disponíveis para matrícula (alunos)
- ✅ **`enroll()`** - Atualizado para completar a lógica de matrícula
  - Valida se o aluno já está matriculado
  - Verifica vagas disponíveis
  - Valida prazos de inscrição
  - Cria o registro de matrícula

#### `app/Http/Controllers/RegistrationController.php`
- ✅ **`my()`** - Nova ação para alunos visualizarem seus cursos matriculados
- ✅ **`cancel()`** - Nova ação para alunos cancelarem suas matrículas

### 3. **Rotas (Routes)**

#### `routes/web.php`
```php
// Rotas para alunos (usuários autenticados)
Route::get('cursos/list', [CursoController::class, 'list'])->name('cursos.list');
Route::post('cursos/{curso}/enroll', [CursoController::class, 'enroll'])->name('cursos.enroll');
Route::get('registrations/my', [RegistrationController::class, 'my'])->name('registrations.my');
Route::delete('registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])->name('registrations.cancel');

// Rotas para admin (mantidas como estavam)
Route::resource('cursos', CursoController::class);
Route::resource('registrations', RegistrationController::class);
```

### 4. **Views (Interfaces)**

#### `resources/views/layouts/sidebar.blade.php`
- ✅ Menu condicional baseado no role do usuário
- **Para Alunos:**
  - "Matricular em Cursos" → `cursos.list`
  - "Meus Cursos" → `registrations.my`
- **Para Admin:**
  - "Cursos" → `cursos.index`
  - "Students" → `students.index`
  - "Registrations" → `registrations.index`

#### `resources/views/cursos/list.blade.php` (NOVO)
- ✅ Grid de cursos disponíveis para matrícula
- ✅ Filtros de busca e tipo de curso
- ✅ Condições:
  - Botão "Matricular" para cursos disponíveis
  - "Matriculado" para cursos já inscritos
  - "Inscrição Encerrada" se prazo passou
  - "Sem Vagas" se não há mais vagas

#### `resources/views/registrations/my.blade.php` (NOVO)
- ✅ Lista dos cursos em que o aluno está matriculado
- ✅ Opção de cancelar inscrição
- ✅ Link para explorar mais cursos
- ✅ Mensagem quando aluno não tem cursos

#### `resources/views/cursos/index.blade.php`
- ✅ Atualizado para mostrar botão "Matricular" para alunos
- ✅ Botões "Editar" e "Deletar" apenas para admins

#### `resources/views/dashboard.blade.php`
- ✅ Dashboard customizado para alunos:
  - Card com "Meus Cursos" (quantidade)
  - Card com "Cursos Disponíveis" (quantidade)
  - Lista dos últimos 3 cursos matriculados
- ✅ Dashboard customizado para admins:
  - Cards com estatísticas totais

#### `resources/views/cursos/enroll.blade.php` (Mantido)
- Pode ser usado como confirmação antes de matricular

### 5. **Banco de Dados (Migrations)**

#### `database/migrations/2026_02_19_235959_add_user_id_to_registrations_table.php` (NOVA)
```php
- user_id (nullable, FK para users)
- students_id (nullable)
```
- ✅ Permite matrículas tanto de usuários comuns quanto de Students

## Fluxo de Uso

### 1. **Para um Aluno**

1. Faz login como usuário comum (não admin)
2. Vê o Dashboard com resumo de cursos
3. Clica em "Matricular em Cursos" no sidebar
4. Visualiza a lista de cursos disponíveis (com filtros)
5. Clica em "Matricular" em um curso
6. Sistema valida disponibilidade e cria a matrícula
7. Acessa "Meus Cursos" para ver seus cursos matriculados
8. Pode cancelar uma inscrição se necessário

### 2. **Validações Realizadas**

- ✅ Impede matrícula duplicada no mesmo curso
- ✅ Verifica se há vagas disponíveis
- ✅ Valida prazos de inscrição
- ✅ Autoriza apenas admin a gerenciar alunos/matrículas
- ✅ Autoriza apenas o próprio aluno a cancelar sua matrícula

## Tecnologias Utilizadas

- **Framework:** Laravel 11
- **ORM:** Eloquent
- **Database:** MySQL
- **Templating:** Blade
- **Styling:** Tailwind CSS

## Como Testar

1. **Criar um usuário comum:**
```bash
sail artisan tinker
$user = User::create(['name' => 'João Silva', 'email' => 'joao@test.com', 'password' => Hash::make('123456'), 'role' => 'user']);
exit;
```

2. **Acessar o sistema:**
   - Fazer login com esse usuário
   - Clicar em "Matricular em Cursos"
   - Selecionar um curso e matricular
   - Visualizar em "Meus Cursos"

3. **Verificar no banco:**
```bash
sail artisan tinker
$registrations = Registration::where('user_id', 1)->with('curso')->get();
$registrations->each(fn($r) => echo $r->curso->name . "\n");
exit;
```

## Status

✅ **Implementação Completa**
- Todas as funcionalidades estão operacionais
- Banco de dados migrado
- Interfaces criadas
- Validações implementadas

## Próximas Enhancements Possíveis

- 📧 Enviar email de confirmação para matrícula
- 📊 Dashboard com histórico de cursos completados
- ⭐ Sistema de avaliação de cursos
- 📱 Responsividade móvel aprimorada
- 🔔 Notificações de novos cursos
