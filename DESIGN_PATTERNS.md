# Arquitetura e Design Patterns - Aplicação de Matrículas

## 📋 Visão Geral

Esta aplicação implementa os seguintes padrões de design para garantir uma arquitetura limpa, escalável e manutenível:

1. **Repository Pattern** - Abstração de dados
2. **Singleton Pattern** - Instâncias únicas para Managers
3. **Adapter Pattern** - Sistema de notificações flexível
4. **Observer Pattern** - Eventos e reações automáticas
5. **Service Layer Pattern** - Lógica de negócio centralizada

---

## 🎯 Padrões de Design Implementados

### 1. Repository Pattern

**Propósito**: Abstrair a lógica de acesso a dados e fornecer uma interface consistente para operações CRUD.

**Localização**: `app/Repositories/`

#### Estrutura
```
Repositories/
├── RepositoryInterface.php       # Contrato que todos os repositórios implementam
├── BaseRepository.php             # Implementação base reutilizável
├── CursoRepository.php            # Repositório específico para Cursos
├── StudentRepository.php          # Repositório específico para Students
└── RegistrationRepository.php     # Repositório específico para Registrations
```

#### Benefícios
- ✅ Testabilidade - Fácil mockar a camada de dados
- ✅ Manutenibilidade - Mudanças no banco não afetam Controllers
- ✅ Reutilização - Métodos comuns em BaseRepository
- ✅ Consistência - Interface uniforme para todas as entidades

#### Exemplo de Uso
```php
// Injetar repositório no controller
public function __construct(CursoRepository $repository)
{
    $this->repository = $repository;
}

// Usar métodos abstratos
$cursos = $this->repository->all();
$curso = $this->repository->find($id);
$paginated = $this->repository->paginate(15);
$results = $this->repository->search('Laravel', ['name', 'description']);
```

---

### 2. Singleton Pattern

**Propósito**: Garantir apenas uma única instância de classes críticas em toda aplicação.

**Localização**: `app/Managers/`

#### Managers Implementados

##### AuthManager
```php
// Acesso centralizado de autenticação
$auth = AuthManager::getInstance();
$auth->login($credentials);
$auth->isAdmin();
$auth->getToken();
```

**Responsabilidades**:
- Login / Logout
- JWT token management
- Verificação de roles
- Context de usuário autenticado

##### MailManager
```php
// Gerenciamento centralizado de emails
$mail = MailManager::getInstance();
$mail->send($email, $subject, $view, $data);
$mail->sendWelcome($email, $name);
$mail->sendRegistrationConfirmation($email, $studentName, $cursoName);
```

**Responsabilidades**:
- Envio de emails
- Templates de notificação
- Retry logic
- Logging de envios

##### CacheManager
```php
// Gerenciamento centralizado de cache
$cache = CacheManager::getInstance();
$cache->put('key', $value, 3600);
$cache->remember('key', callback, 3600);
$cache->invalidateUsers();
$cache->invalidateCursos();
```

**Responsabilidades**:
- Armazena dados em cache
- Invalidação automática
- Cache patterns específicos (users, courses, etc)
- TTL management

#### Benefícios
- 🔒 Thread-safe access
- 💾 Uma única instância em memória
- 🎯 Ponto de controle centralizado
- 🔄 Fácil de substituir/mockar para testes

---

### 3. Adapter Pattern

**Propósito**: Permitir múltiplas formas de enviar notificações sem modificar código existente.

**Localização**: `app/Adapters/`

#### Estrutura
```
Adapters/
├── NotificationAdapterInterface.php    # Contrato para adaptadores
├── EmailNotificationAdapter.php        # Envia via Email
├── LogNotificationAdapter.php          # Envia via Log
├── DatabaseNotificationAdapter.php     # Armazena em DB
└── NotificationManager.php             # Factory e orquestrador
```

#### Adapters Disponíveis

**EmailNotificationAdapter**
- Envia notificações via email
- Usa Laravel Mail facade
- Configuração em `config/mail.php`

**LogNotificationAdapter**
- Registra notificações em log
- Sempre disponível
- Ideal para debugging

**DatabaseNotificationAdapter**
- Armazena notificações em banco de dados
- Permite histórico e análise
- Requer tabela `notifications`

#### Exemplo de Uso
```php
// Usar adaptador único
$notificationManager = new NotificationManager();
$notificationManager->send($email, $subject, $message);

// Usar adaptador específico
$notificationManager->via('email')->send($email, $subject, $message);

// Enviar via múltiplos adaptadores
$notificationManager->sendMultiple(
    ['email', 'database', 'log'],
    $email,
    $subject,
    $message
);

// Verificar disponibilidade
$available = $notificationManager->getAvailableAdapters();
```

#### Benefícios
- 📡 Suporta múltiplos canais
- 🔌 Fácil adicionar novos adaptadores
- 🎯 Sem modificação de código existente
- 🧪 Cada adaptador isolado e testável

---

### 4. Observer Pattern

**Propósito**: Reagir automaticamente a eventos do modelo sem coplar lógica aos modelos.

**Localização**: `app/Observers/`

#### Observers Implementados

**CursoObserver**
```php
// Dispara quando curso é criado
created(Curso $curso)
  ├─ Invalida cache
  └─ Registra em log

// Dispara quando curso é atualizado
updated(Curso $curso)
  ├─ Invalida cache
  └─ Registra em log

// Dispara quando curso é deletado
deleted(Curso $curso)
  ├─ Invalida cache
  └─ Registra aviso

// Dispara quando curso é restaurado
restored(Curso $curso)
  └─ Registra em log
```

**StudentObserver**
```php
// Dispara quando aluno é criado
created(Student $student)
  ├─ Invalida cache
  ├─ Envia email de boas-vindas
  └─ Registra em log

// Dispara quando aluno é atualizado
updated(Student $student)
  ├─ Invalida cache
  └─ Registra em log

// Dispara quando aluno é deletado
deleted(Student $student)
  ├─ Invalida cache
  └─ Registra aviso
```

**RegistrationObserver**
```php
// Dispara quando matrícula é criada
created(Registration $registration)
  ├─ Invalida cache
  ├─ Envia email de confirmação
  ├─ Envia notificação (múltiplos canais)
  └─ Registra em log

// Dispara quando matrícula é deletada
deleted(Registration $registration)
  ├─ Invalida cache
  ├─ Notifica cancelamento
  └─ Registra em log
```

#### Exemplo de Uso
```php
// Observers registrados automaticamente em AppServiceProvider
Curso::observe(CursoObserver::class);
Student::observe(StudentObserver::class);
Registration::observe(RegistrationObserver::class);

// Uso transparente - eventos disparam automaticamente
$student = Student::create($data);  // Dispara created event
$student->update($newData);          // Dispara updated event
$student->delete();                  // Dispara deleted event
```

#### Benefícios
- 🔄 Reactions automáticas a eventos
- 📜 Model listeners separados
- 🧹 Código limpo e desacoplado
- 🔍 Fácil adicionar/remover listeners

---

### 5. Service Layer Pattern

**Propósito**: Centralizar lógica de negócio complexa em serviços reutilizáveis.

**Localização**: `app/Services/`

#### Serviços Implementados

**CursoService**
```php
// Métodos disponíveis
getAllCursos()              // Com cache
getCursoById($id)
getAvailableCursos()        // Cursos com vagas
createCurso($data)
updateCurso($id, $data)
deleteCurso($id)
deleteCursos($ids)          // Deletar múltiplos
searchCursos($query)        // Busca em name + description
```

**StudentService**
```php
getAllStudents()            // Com cache
getStudentById($id)
getStudentWithCourses($id)  // Inclui relacionamento
getActiveStudents()
createStudent($data)        // Notifica criação
updateStudent($id, $data)
deleteStudent($id)
deleteStudents($ids)
searchStudents($query)      // Busca em name + email
```

**RegistrationService**
```php
getAllRegistrations()
getRegistrationById($id)
getRegistrationsByStudent($studentId)
enrollStudent($studentId, $cursoId)     // Validações de negócio
  ├─ Verifica se já matriculado
  ├─ Verifica vagas disponíveis
  ├─ Verifica deadline
  └─ Decrementa vagas
cancelEnrollment($registrationId)       // Remove com reversal
deleteRegistrations($ids)
```

#### Exemplo de Uso
```php
// Injetar no controller
public function __construct(CursoService $service)
{
    $this->service = $service;
}

// Usar lógica centralizada
$curso = $this->service->createCurso($data);
$all = $this->service->getAllCursos();
$available = $this->service->getAvailableCursos();
```

#### Benefícios
- 📦 Lógica de negócio centralizada
- 🔄 Reutilizável em múltiplos controllers
- 🧪 Fácil testar
- 🎯 Controllers finos e focados

---

## 📁 Estrutura de Diretórios

```
app/
├── Repositories/           # Abstração de dados
│   ├── RepositoryInterface.php
│   ├── BaseRepository.php
│   ├── CursoRepository.php
│   ├── StudentRepository.php
│   └── RegistrationRepository.php
│
├── Services/              # Lógica de negócio
│   ├── CursoService.php
│   ├── StudentService.php
│   └── RegistrationService.php
│
├── Managers/              # Singletons críticos
│   ├── AuthManager.php
│   ├── MailManager.php
│   └── CacheManager.php
│
├── Adapters/              # Adaptadores de notificação
│   ├── NotificationAdapterInterface.php
│   ├── EmailNotificationAdapter.php
│   ├── LogNotificationAdapter.php
│   ├── DatabaseNotificationAdapter.php
│   └── NotificationManager.php
│
├── Observers/             # Listeners de modelo
│   ├── CursoObserver.php
│   ├── StudentObserver.php
│   └── RegistrationObserver.php
│
├── Http/
│   ├── Controllers/       # Controllers finos (injetam Services)
│   │   ├── CursoController.php
│   │   ├── StudentController.php
│   │   ├── RegistrationController.php
│   │   └── AuthenticationController.php
│   ├── Requests/          # Form request validation
│   └── Middleware/        # Auth middleware
│
├── Models/               # Entidades do banco
│   ├── User.php
│   ├── Curso.php
│   ├── Student.php
│   └── Registration.php
│
├── Providers/            # Service providers
│   └── AppServiceProvider.php
│
└── Enums/               # Enumerações
    └── CursoTypes.php
```

---

## 🔗 Fluxo de Requisição

```
1. HTTP Request
   ↓
2. Route → Controller
   ↓
3. Controller injeta Service
   ↓
4. Service usa Repository
   ↓
5. Repository usa Model
   ↓
6. Model dispara Observers
   ↓
7. Observers usam Managers/Adapters
   ↓
8. Response ao Cliente
```

### Exemplo Prático: Criar Aluno

```
POST /students
  ↓
StudentController::store()
  ├─ Valida Form Request
  ├─ Injeta StudentService
  ├─ Chama $service->createStudent($data)
  │  ├─ Repository::create($data)
  │  │  └─ Student::create() no banco
  │  │     └─ StudentObserver::created() dispara
  │  │        ├─ CacheManager::invalidateUsers()
  │  │        ├─ MailManager::sendWelcome()
  │  │        └─ Log event
  │  └─ Notificação via NotificationManager
  └─ Retorna Student criado
```

---

## 🧪 Testabilidade

Cada camada é testável isoladamente:

```php
// Teste de Repository
$repository = new CursoRepository(new Curso());
$curso = $repository->create(['name' => 'Laravel']);
$this->assertEquals('Laravel', $curso->name);

// Teste de Service (com mocks)
$repositoryMock = Mockery::mock(CursoRepository::class);
$service = new CursoService($repositoryMock, ...);
$repositoryMock->shouldReceive('all')->once();
$service->getAllCursos();

// Teste de Manager (Singleton)
$auth = AuthManager::getInstance();
$token = $auth->login($credentials);
$this->assertNotNull($token);
```

---

## 🛠️ Configuração e Uso

### Registrar no AppServiceProvider
Todos os componentes estão pré-registrados em `App\Providers\AppServiceProvider`:

```php
// Repositories - Singletons
$this->app->singleton(CursoRepository::class, ...);
$this->app->singleton(StudentRepository::class, ...);
$this->app->singleton(RegistrationRepository::class, ...);

// Services - Singletons
$this->app->singleton(CursoService::class, ...);
$this->app->singleton(StudentService::class, ...);
$this->app->singleton(RegistrationService::class, ...);

// Managers - Singletons
$this->app->singleton(AuthManager::class, ...);
$this->app->singleton(MailManager::class, ...);
$this->app->singleton(CacheManager::class, ...);

// Observers
Curso::observe(CursoObserver::class);
Student::observe(StudentObserver::class);
Registration::observe(RegistrationObserver::class);
```

### Usar em Controllers
```php
public function __construct(
    CursoService $cursoService,
    StudentService $studentService,
    RegistrationService $registrationService
) {
    $this->cursoService = $cursoService;
    $this->studentService = $studentService;
    $this->registrationService = $registrationService;
}

public function index()
{
    $cursos = $this->cursoService->getAllCursos();
    return view('cursos.index', compact('cursos'));
}
```

### Usar Managers Anywhere
```php
// Singleton acessível de qualquer lugar
$auth = AuthManager::getInstance();
$mail = MailManager::getInstance();
$cache = CacheManager::getInstance();
```

---

## ✨ Exemplos Completos

### Exemplo 1: Criar Aluno com Validações

```php
// Service
public function createStudent(array $data)
{
    $student = $this->repository->create($data);
    
    // Notifica via múltiplos canais
    $this->notificationManager->sendMultiple(
        ['email', 'database'],
        $data['email'],
        'Bem-vindo!',
        "Aluno {$data['name']} cadastrado."
    );

    return $student;
}

// Observer intercepta automaticamente
Student::observe(StudentObserver::class);
// Ao criar: envia email, invalida cache, registra log
```

### Exemplo 2: Matricular Aluno com Regras de Negócio

```php
// Service com validações
public function enrollStudent(int $studentId, int $cursoId): bool
{
    // Verificação 1: Não duplicado
    if ($this->repository->isEnrolled($studentId, $cursoId)) {
        throw ValidationException::withMessages([...]);
    }

    // Verificação 2: Vagas disponíveis
    $curso = $this->cursoRepository->find($cursoId);
    if ($curso->available_spots <= 0) {
        throw ValidationException::withMessages([...]);
    }

    // Verificação 3: Deadline
    if ($curso->registration_deadline < now()) {
        throw ValidationException::withMessages([...]);
    }

    // Executar matrícula
    $registration = $this->repository->create([...]);
    $curso->decrement('available_spots');

    return true;
}
```

### Exemplo 3: Buscar com Cache

```php
// Service com cache automático
public function getAllCursos()
{
    return $this->cacheManager->remember('all_cursos', function () {
        return $this->repository->all();
    });
}

// Cache invalidado automaticamente ao criar/atualizar
Curso::observe(CursoObserver::class);
// created() → invalidateCursos()
// updated() → invalidateCursos()
```

---

## 📊 Benefícios da Arquitetura

| Aspecto | Benefício |
|--------|----------|
| **Manutenibilidade** | Código organizado em camadas com responsabilidades claras |
| **Testabilidade** | Cada camada isolada facilita testes unitários |
| **Escalabilidade** | Adicionar features sem quebrar código existente |
| **Reutilização** | Services e Repositories reutilizáveis em múltiplos contexts |
| **Flexibilidade** | Adapters permitem múltiplas implementações |
| **Debugging** | Fluxo bem definido facilita diagnóstico |
| **Documentação** | Padrões conhecidos são auto-documentados |
| **Performance** | Cache e Singletons otimizam acesso |

---

## 📚 Referências

- Repository Pattern: https://refactoring.guru/design-patterns/repository
- Singleton Pattern: https://refactoring.guru/design-patterns/singleton
- Adapter Pattern: https://refactoring.guru/design-patterns/adapter
- Observer Pattern: https://refactoring.guru/design-patterns/observer
- Service Layer: https://martinfowler.com/eaaCatalog/serviceLayer.html

---

**Última atualização**: 19 de Fevereiro de 2026
