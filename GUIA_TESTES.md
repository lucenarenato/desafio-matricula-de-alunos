# 🧪 Como Executar os Testes

## Pré-requisitos

- Docker e Docker Compose instalados
- Projeto Laravel 11 configurado com Sail
- Banco de dados MySQL rodando

## Executar Todos os Testes

```bash
./vendor/bin/sail artisan test
```

**Saída esperada:**
```
Tests: 175, Assertions: 372
OK (175 tests, 372 assertions)
```

## Executar com Relatório Testdox

Mostra um relatório mais legível com nomes dos testes:

```bash
./vendor/bin/sail artisan test --testdox
```

## Executar Testes Específicos

### Por arquivo
```bash
./vendor/bin/sail artisan test tests/Feature/CursoControllerTest.php
./vendor/bin/sail artisan test tests/Unit/Models/UserTest.php
```

### Por nome do teste
```bash
./vendor/bin/sail artisan test --filter test_store_creates_curso
./vendor/bin/sail artisan test --filter "StudentValidation"
```

### Apenas testes unitários
```bash
./vendor/bin/sail artisan test tests/Unit
```

### Apenas testes de feature
```bash
./vendor/bin/sail artisan test tests/Feature
```

## Cobertura de Código

Gerar relatório de cobertura:

```bash
./vendor/bin/sail artisan test --coverage
```

Com arquivo HTML:
```bash
./vendor/bin/sail artisan test --coverage --coverage-html=coverage
```

## Opções Úteis

### Parar na primeira falha
```bash
./vendor/bin/sail artisan test --stop-on-failure
```

### Output verboso
```bash
./vendor/bin/sail artisan test --verbose
```

### Mostrar apenas falhas
```bash
./vendor/bin/sail artisan test --quiet
```

### Executar em paralelo
```bash
./vendor/bin/sail artisan test --parallel
```

## Estrutura dos Testes

```
tests/
├── Unit/
│   └── Models/
│       ├── CursoTest.php          (23 testes)
│       ├── StudentTest.php        (14 testes)
│       ├── RegistrationTest.php   (10 testes)
│       └── UserTest.php           (13 testes)
└── Feature/
    ├── CursoControllerTest.php           (16 testes)
    ├── StudentControllerTest.php         (16 testes)
    ├── RegistrationControllerTest.php    (15 testes)
    ├── AuthenticationTest.php            (22 testes)
    ├── CursoValidationTest.php           (13 testes)
    ├── StudentValidationTest.php         (14 testes)
    └── RegistrationValidationTest.php    (14 testes)
```

## O que é Testado?

### Testes Unitários (60 testes)
- ✅ Operações CRUD de cada modelo
- ✅ Relacionamentos entre modelos
- ✅ Validações de dados
- ✅ Métodos customizados
- ✅ Cascade delete

### Testes de Feature (115 testes)
- ✅ Endpoints CRUD completos
- ✅ Validação de formulários
- ✅ Autenticação e autorização
- ✅ Proteção de rotas
- ✅ Paginação e busca
- ✅ Regras de negócio (inscrição duplicada, vagas cheias, deadline)

## Exemplo: Testar Validação de Curso

Para executar apenas testes de validação de curso:

```bash
./vendor/bin/sail artisan test tests/Feature/CursoValidationTest.php --testdox
```

Saída:
```
Curso Validation (Tests\Feature\CursoValidation)
 ✔ Name is required
 ✔ Name must be unique
 ✔ Name must not exceed 255 chars
 ✔ Type is required
 ✔ Type must be valid
 ✔ Maximum enrollments is required
 ✔ Maximum enrollments must be positive integer
 ✔ Registration deadline is required
 ✔ Registration deadline must be in future
 ✔ Description is optional
 ✔ Description is optional and not limited
 ✔ Valid curso creation
 ✔ Successful creation stores data
```

## Exemplo: Testar Autenticação

Para executar apenas testes de autenticação:

```bash
./vendor/bin/sail artisan test tests/Feature/AuthenticationTest.php --testdox
```

## Resetar Banco de Dados para Testes

Os testes usam `RefreshDatabase` trait que:
- Reseta o banco antes de cada teste
- Garante isolamento de testes
- Não afeta dados de produção

Mas se precisar resetar manualmente:

```bash
./vendor/bin/sail artisan migrate:fresh
```

## Debug de Testes

### Ver detalhes de uma falha
```bash
./vendor/bin/sail artisan test tests/Feature/CursoControllerTest.php::test_store_creates_curso --verbose
```

### Usar dd() em testes
```php
public function test_example(): void
{
    $response = $this->actingAs($this->admin)->get(route('cursos.index'));
    dd($response->content()); // Para debug
}
```

### Verificar banco de dados durante teste
```php
$this->assertDatabaseHas('cursos', ['name' => 'Test']);
```

## Pipeline CI/CD

Para integrar com CI/CD (GitHub Actions, GitLab CI, etc):

```bash
./vendor/bin/sail artisan test --coverage --coverage-text
```

## Checklist de Qualidade

Antes de fazer commit:

- [ ] Todos os 175 testes passam
- [ ] Nenhum teste foi marcado como skipped
- [ ] Cobertura de testes acima de 90%
- [ ] Nenhum warning ou notice nos logs de teste

```bash
# Executar com relatório completo
./vendor/bin/sail artisan test --testdox --coverage
```

## Solução de Problemas

### Erro: "SQLSTATE[HY000]: General error: 2..."
**Solução**: Resetar banco de dados
```bash
./vendor/bin/sail artisan migrate:fresh
```

### Erro: "Class does not exist..."
**Solução**: Recompilar autoloader
```bash
./vendor/bin/sail composer dump-autoload
```

### Erro: "Timeout..."
**Solução**: Aumentar timeout do teste
```bash
./vendor/bin/sail artisan test --timeout=300
```

---

**Dúvidas?** Veja [TESTES_DETALHADO.md](TESTES_DETALHADO.md) para documentação completa.
