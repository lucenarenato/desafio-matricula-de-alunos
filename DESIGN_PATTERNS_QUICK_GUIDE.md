# 🚀 Design Patterns - Quick Reference Guide

## 5 Minutos para Entender a Arquitetura

### Regra de Ouro
```
Controller → Service → Repository → Model → Observer
```

---

## 1️⃣ Repository Pattern

**Use quando**: Precisa acessar dados

```php
// ❌ ERRADO
public function index() {
    $cursos = Curso::all();  // Acesso direto ao modelo
}

// ✅ CERTO
public function __construct(CursoRepository $repository) {
    $this->repository = $repository;
}

public function index() {
    $cursos = $this->repository->all();  // Via repositório
}
```

**Métodos úteis**:
```php
$repo->all()                    // Todos
$repo->find($id)                // Por ID
$repo->paginate(15)             // Com paginação
$repo->search($query, ['name']) // Busca
$repo->create($data)            // Criar
$repo->update($id, $data)       // Atualizar
$repo->delete($id)              // Deletar
$repo->deleteMany($ids)         // Múltiplos
```

---

## 2️⃣ Singleton Managers

**Use quando**: Precisa de instância única e centralizada

```php
// ❌ ERRADO
$auth1 = new AuthManager();
$auth2 = new AuthManager();  // Diferentes instâncias!

// ✅ CERTO
$auth1 = AuthManager::getInstance();
$auth2 = AuthManager::getInstance();  // Mesma instância
```

### AuthManager
```php
$auth = AuthManager::getInstance();

$token = $auth->login($credentials);      // Login
$auth->logout();                          // Logout
$user = $auth->user();                    // Usuário atual
$auth->isAdmin();                         // Verificar role
```

### MailManager
```php
$mail = MailManager::getInstance();

$mail->send($email, $subject, $view, $data);
$mail->sendWelcome($email, $name);
$mail->sendRegistrationConfirmation($email, $student, $curso);
```

### CacheManager
```php
$cache = CacheManager::getInstance();

$cache->put('key', $value, 3600);         // Guardar
$cache->get('key');                       // Recuperar
$cache->remember('key', callback, 3600);  // Guardar se não existe
$cache->forget('key');                    // Remover
$cache->invalidateUsers();                // Limpar users - automático!
$cache->invalidateCursos();               // Limpar cursos - automático!
```

---

## 3️⃣ Adapter Pattern

**Use quando**: Múltiplas formas de fazer a mesma coisa

```php
// NotificationManager suporta múltiplos adaptadores
$notif = new NotificationManager();

// Adaptador padrão (geralmente Email)
$notif->send($email, $subject, $message);

// Adaptador específico
$notif->via('email')->send(...);
$notif->via('log')->send(...);
$notif->via('database')->send(...);

// Múltiplos adaptadores
$notif->sendMultiple(['email', 'database'], $email, $subject, $message);
```

**Disponíveis**:
- 📧 `EmailNotificationAdapter` - Email
- 📝 `LogNotificationAdapter` - Log/Arquivo
- 💾 `DatabaseNotificationAdapter` - Banco de dados

---

## 4️⃣ Observer Pattern

**Use quando**: Algo acontece no modelo, execute ações

```
Modelo             Observador              Ações
─────────────────────────────────────────────────────
Student::create()  → StudentObserver        ├─ Envia email
                                            ├─ Invalida cache
                                            └─ Registra log

Curso::update()    → CursoObserver          ├─ Invalida cache
                                            └─ Registra log

Registration::delete() → RegistrationObserver ├─ Invalida cache
                                              ├─ Notifica
                                              └─ Registra log
```

**Registrado automaticamente** em `AppServiceProvider`:
```php
Curso::observe(CursoObserver::class);
Student::observe(StudentObserver::class);
Registration::observe(RegistrationObserver::class);
```

---

## 5️⃣ Service Layer

**Use quando**: Lógica de negócio complexa

```php
// ❌ ERRADO - Lógica no Controller
public function store(Request $request) {
    if (Registration::where('student_id', ...)->exists()) {
        throw new Exception("Já matriculado");
    }
    
    if ($curso->available_spots <= 0) {
        throw new Exception("Sem vagas");
    }
    
    Registration::create([...]);
    $curso->decrement('available_spots');
}

// ✅ CERTO - Lógica no Service
public function __construct(RegistrationService $service) {
    $this->service = $service;
}

public function store(Request $request) {
    $this->service->enrollStudent($studentId, $cursoId);
}
```

**Services disponíveis**:
```php
CursoService::
  getAllCursos()
  getCursoById($id)
  getAvailableCursos()
  createCurso($data)
  searchCursos($query)

StudentService::
  getAllStudents()
  getStudentById($id)
  getStudentWithCourses($id)
  createStudent($data)
  searchStudents($query)

RegistrationService::
  enrollStudent($studentId, $cursoId)     // Com validações!
  cancelEnrollment($registrationId)
  getRegistrationsByStudent($studentId)
```

---

## 📊 Decisão Rápida

| Preciso... | Use... |
|-----------|--------|
| Acessar dados | **Repository** |
| Gerenciar auth/mail/cache | **Manager (Singleton)** |
| Enviar notificação de múltiplas formas | **Adapter** |
| Reagir a eventos do modelo | **Observer** |
| Lógica de negócio complexa | **Service** |
| Formatar validação de entrada | **FormRequest** |

---

## 🔗 Fluxo de Exemplo: Matricular Aluno

```
POST /registrations
  ↓
RegistrationController::store()
  ├─ Injeta RegistrationService
  ├─ Chiama $service->enrollStudent($studentId, $cursoId)
  │  ├─ Valida: Não duplicado?
  │  ├─ Valida: Tem vagas?
  │  ├─ Valida: Prazo aberto?
  │  ├─ Repository::create() (salva BD)
  │  │  └─ Dispara RegistrationObserver::created()
  │  │     ├─ CacheManager::invalidateUsers()
  │  │     ├─ MailManager::sendRegistrationConfirmation()
  │  │     ├─ NotificationManager::sendMultiple(['email', 'database'])
  │  │     └─ Log event
  │  └─ Decrementa vagas no curso
  └─ Retorna Response
```

---

## 💡 Tips & Tricks

### 1. Sempre injetar, nunca instanciar
```php
// ❌
$service = new CursoService(...);

// ✅
public function __construct(CursoService $service) {
    $this->service = $service;
}
```

### 2. Managers acessíveis globalmente
```php
$auth = AuthManager::getInstance();  // De qualquer lugar
$cache = CacheManager::getInstance();
$mail = MailManager::getInstance();
```

### 3. Cache invalidado automaticamente
```php
// Ao criar/atualizar/deletar, cache é invalidado automaticamente
Student::create($data);      // Observer invalida cache
$student->update($data);     // Observer invalida cache
$student->delete();          // Observer invalida cache
```

### 4. Múltiplos adaptadores de notificação
```php
// Enviar email + log + database
$notif->sendMultiple(
    ['email', 'log', 'database'],
    $recipient,
    $subject,
    $message
);
```

### 5. Usar .remember() para cache automático
```php
$cursos = $cache->remember('all_cursos', function () {
    return Curso::all();
}, 3600);

// Se estiver em cache, retorna cache
// Se não estiver, executa callback, salva e retorna
```

---

## 🧪 Testando

```php
// Mock Repository
$repositoryMock = Mockery::mock(CursoRepository::class);
$repositoryMock->shouldReceive('all')->once()->andReturn([...]);

// Injetar no Service
$service = new CursoService($repositoryMock, ...);

// Testar
$cursos = $service->getAllCursos();
$this->assertCount(3, $cursos);
```

---

## 📚 Para Saber Mais

Ver `DESIGN_PATTERNS.md` para documentação completa com:
- Arquitetura detalhada
- Exemplos práticos
- Benefícios de cada padrão
- Estrutura de diretórios
- Referências

---

**Lembre-se**: O objetivo é **código limpo, testável e manutenível** 🎯
