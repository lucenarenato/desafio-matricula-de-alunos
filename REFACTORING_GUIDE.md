# 🔄 Refactoring Controllers - Updated Architecture

## Antes vs Depois

Dois exemplos comparando a arquitetura antiga com a nova usando design patterns.

---

## Exemplo 1: Curso Controller - List Action

### ❌ ANTES (Sem Padrões)

```php
class CursoController extends Controller
{
    public function index(Request $request)
    {
        // Lógica acoplada ao controller
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        
        $query = Curso::query();
        
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }
        
        $cursos = $query->paginate($perPage);
        
        return view('cursos.index', compact('cursos'));
    }
}
```

### ✅ DEPOIS (Com Padrões)

```php
class CursoController extends Controller
{
    protected CursoService $service;

    public function __construct(CursoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Lógica centralizada no Service → Repository
        $cursos = $this->service->getAllCursos();
        
        return view('cursos.index', compact('cursos'));
    }
}
```

**Benefícios**:
- Controller focado apenas em HTTP
- Lógica reutilizável em muitos lugares
- Fácil testar a lógica independente
- Cache automático
- Melhor manutenção

---

## Exemplo 2: Registration Controller - Store Action

### ❌ ANTES (Sem Padrões)

```php
class RegistrationController extends Controller
{
    public function store(StoreRegistrationRequest $request)
    {
        $validated = $request->validated();
        
        $studentId = $validated['student_id'];
        $cursoId = $validated['curso_id'];
        
        // Validação de negócio no controller
        if (Registration::where('student_id', $studentId)
                        ->where('curso_id', $cursoId)
                        ->exists()) {
            return back()->with('error', 'Já matriculado');
        }
        
        $curso = Curso::find($cursoId);
        if (!$curso || $curso->available_spots <= 0) {
            return back()->with('error', 'Sem vagas');
        }
        
        if ($curso->registration_deadline && 
            $curso->registration_deadline < now()) {
            return back()->with('error', 'Prazo expirado');
        }
        
        // Criar matrícula
        $registration = Registration::create([
            'student_id' => $studentId,
            'curso_id' => $cursoId,
            'enrolled_at' => now(),
        ]);
        
        // Atualizar vagas
        $curso->decrement('available_spots');
        
        // Enviar email manualmente
        $student = Student::find($studentId);
        Mail::send('emails.registration', [
            'student' => $student,
            'curso' => $curso
        ], function ($msg) use ($student) {
            $msg->to($student->email);
        });
        
        return redirect('registrations')
            ->with('success', 'Matrícula confirmada');
    }
}
```

### ✅ DEPOIS (Com Padrões)

```php
class RegistrationController extends Controller
{
    protected RegistrationService $service;

    public function __construct(RegistrationService $service)
    {
        $this->service = $service;
    }

    public function store(StoreRegistrationRequest $request)
    {
        try {
            $validated = $request->validated();
            
            // Service cuida de toda validação e lógica
            $this->service->enrollStudent(
                $validated['student_id'],
                $validated['curso_id']
            );
            
            return redirect('registrations')
                ->with('success', 'Matrícula confirmada');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
```

**Benefícios**:
- Validações centralizadas no Service
- Email enviado automaticamente via Observer
- Cache invalidado automaticamente
- Código bem mais limpo
- Fácil de entender e manter
- Reutilizável em API, Commands, etc

---

## Exemplo 3: Student Controller - Create Action

### ❌ ANTES (Sem Padrões)

```php
class StudentController extends Controller
{
    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();
        
        // Criar aluno
        $student = Student::create($validated);
        
        // Enviar email manualmente
        try {
            Mail::send('emails.welcome', ['name' => $student->name], 
                function ($msg) use ($student) {
                    $msg->to($student->email);
                }
            );
        } catch (Exception $e) {
            // Ignorar erro de email
        }
        
        return redirect('students')
            ->with('success', 'Aluno criado com sucesso');
    }
}
```

### ✅ DEPOIS (Com Padrões)

```php
class StudentController extends Controller
{
    protected StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function store(StoreStudentRequest $request)
    {
        $student = $this->service->createStudent(
            $request->validated()
        );
        
        return redirect('students')
            ->with('success', 'Aluno criado com sucesso');
    }
}
```

**O que acontece automaticamente**:
1. `StudentObserver::created()` é disparado
2. Email de boas-vindas é enviado automaticamente
3. Cache de usuários é invalidado
4. Log de evento é registrado
5. Notificações são enviadas via múltiplos canais

**Benefícios**:
- Controllers limpos e simples
- Lógica centralizada
- Tudo automático e testável
- Fácil adicionar novos observers

---

## Padrão Recomendado para Novos Controllers

```php
namespace App\Http\Controllers;

use App\Services\{CursoService, StudentService, RegistrationService};
use App\Http\Requests\{StoreCursoRequest, UpdateCursoRequest};
use Illuminate\Http\Request;

class CursoController extends Controller
{
    protected CursoService $service;

    /**
     * Injetar o Service
     */
    public function __construct(CursoService $service)
    {
        $this->service = $service;
    }

    /**
     * Exibir lista
     */
    public function index()
    {
        $cursos = $this->service->getAllCursos();
        return view('cursos.index', compact('cursos'));
    }

    /**
     * Exibir formulário Create
     */
    public function create()
    {
        return view('cursos.create');
    }

    /**
     * Armazenar novo curso
     */
    public function store(StoreCursoRequest $request)
    {
        $curso = $this->service->createCurso($request->validated());
        
        return redirect('cursos')
            ->with('success', 'Curso criado com sucesso');
    }

    /**
     * Exibir um curso
     */
    public function show(Curso $curso)
    {
        return view('cursos.show', compact('curso'));
    }

    /**
     * Exibir formulário Edit
     */
    public function edit(Curso $curso)
    {
        return view('cursos.edit', compact('curso'));
    }

    /**
     * Atualizar curso
     */
    public function update(UpdateCursoRequest $request, Curso $curso)
    {
        $this->service->updateCurso($curso->id, $request->validated());
        
        return redirect('cursos')
            ->with('success', 'Curso atualizado com sucesso');
    }

    /**
     * Deletar curso
     */
    public function destroy(Curso $curso)
    {
        $this->service->deleteCurso($curso->id);
        
        return redirect('cursos')
            ->with('success', 'Curso deletado com sucesso');
    }

    /**
     * Deletar múltiplos cursos
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        $this->service->deleteCursos($request->input('ids'));
        
        return redirect('cursos')
            ->with('success', 'Cursos deletados com sucesso');
    }
}
```

---

## Migrando Controllers Existentes

### Step 1: Identificar Lógica de Negócio
```php
// ❌ Antes
if (Registration::where(...).exists()) { ... }
$curso->decrement('available_spots');
Mail::send(...);

// ✅ Depois  
// Mover para Service
$this->service->enrollStudent($studentId, $cursoId);
```

### Step 2: Criar/Atualizar Service
```php
class RegistrationService {
    public function enrollStudent($studentId, $cursoId) {
        // Toda lógica aqui
    }
}
```

### Step 3: Injetar Service no Controller
```php
public function __construct(RegistrationService $service) {
    $this->service = $service;
}
```

### Step 4: Simplificar Controller
```php
public function store(Request $request) {
    $this->service->enrollStudent(
        $request->student_id,
        $request->curso_id
    );
}
```

### Step 5: Testar!
```php
// Teste do Service isolado
public function test_enroll_student() {
    $service = new RegistrationService(...);
    $service->enrollStudent(1, 1);
    $this->assertDatabaseHas('registrations', ...);
}
```

---

## Checklist de Refactoring

- [ ] Identificar lógica de negócio no controller
- [ ] Criar/atualizar Service correspondente
- [ ] Mover validações para Service
- [ ] Mover queries do banco para Repository
- [ ] Injetar Service no controller
- [ ] Simplificar controller actions
- [ ] Testar Service isoladamente
- [ ] Testar Controller com Service mockado
- [ ] Remover código duplicado
- [ ] Atualizar em controllers relacionados

---

## Resultado Final

| Aspecto | Antes | Depois |
|--------|-------|--------|
| **Linhas por Controller** | 50-100 | 10-20 |
| **Testabilidade** | Difícil | Fácil |
| **Reutilização** | Nenhuma | Total |
| **Manutenção** | Difícil | Fácil |
| **Escalabilidade** | Limitada | Excelente |
| **Qualidade Código** | ⭐⭐ | ⭐⭐⭐⭐⭐ |

---

**Próximos passos**: 
1. Refatorar controllers existentes gradualmente
2. Escrever testes para Services
3. Adicionar novos padrões conforme necessário
4. Manter documentação atualizada
