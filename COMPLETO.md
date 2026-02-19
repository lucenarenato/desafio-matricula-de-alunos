# Sistema de Matrícula de Alunos em Cursos

Sistema administrativo completo para gerenciar matrículas de alunos em cursos, desenvolvido com Laravel 11 e Tailwind CSS.

## 📋 Funcionalidades

### ✅ CRUD de Cursos
- Criar, editar, listar e deletar cursos
- Cursos podem ser Online ou Presencial
- Data máxima para receber novas matrículas
- Quantidade máxima de vagas
- Visualização de alunos inscritos
- Filtragem por tipo e busca por nome/descrição
- Paginação de 15 itens

### ✅ CRUD de Alunos
- Criar, editar, listar e deletar alunos
- Campos: nome, email, data de nascimento, telefone, endereço
- Cada aluno pode se matricular em um ou mais cursos
- Visualização de cursos inscritos
- Busca por nome, email ou telefone
- Paginação de 15 itens

### ✅ CRUD de Matrículas
- Criar, listar e cancelar matrículas
- Validação de vagas disponíveis
- Validação de data limite de inscrição
- Prevenção de inscrição duplicada
- Busca por aluno ou curso
- Paginação de 15 itens

### ✅ Autenticação e Autorização
- Login com email e senha
- Diferenciação de usuários: Admin e User
- Apenas admins podem acessar o painel administrativo
- Proteção de rotas com middleware

## 🚀 Como Usar

### Pré-requisitos
- Docker e Docker Compose instalados
- Git

### Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/lucenarenato/desafio-matrcula-de-alunos.git
cd desafio-matrcula-de-alunos
```

2. **Copie o arquivo .env**
```bash
cp .env.example .env
```

3. **Levante os containers Docker**
```bash
./vendor/bin/sail up -d
```

4. **Execute as migrations e seeds**
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

5. **Acesse a aplicação**
- URL: http://localhost
- Realize o login

### Credenciais de Teste

**Usuário Admin:**
- Email: `admin@example.com`
- Senha: `password`

**Usuário Normal:**
- Email: `user@example.com`
- Senha: `password`

## 📊 Estrutura do Banco de Dados

### tabela: users
- `id` - Chave primária
- `name` - Nome do usuário
- `email` - Email único
- `password` - Senha hash
- `role` - Enum: 'admin' ou 'user'
- `timestamps`

### Tabela: cursos
- `id` - Chave primária
- `name` - Nome do curso (único)
- `description` - Descrição do curso
- `type` - Enum: 'Online' ou 'Presencial'
- `maximum_enrollments` - Número máximo de inscrições
- `registration_deadline` - Data limite para inscrições
- `timestamps`

### Tabela: students
- `id` - Chave primária
- `name` - Nome do aluno
- `email` - Email único
- `date_of_birth` - Data de nascimento
- `phone` - Telefone (opcional)
- `address` - Endereço (opcional)
- `timestamps`

### Tabela: registrations
- `id` - Chave primária
- `students_id` - FK para students
- `cursos_id` - FK para cursos
- `timestamps`

## 🎨 Tecnologias Utilizadas

- **Backend:** Laravel 11
- **Frontend:** Blade Templates + Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze
- **ORM:** Eloquent
- **Containerização:** Docker & Docker Compose

## 📁 Estrutura de Pastas

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CursoController.php
│   │   │   ├── StudentController.php
│   │   │   └── RegistrationController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/
│   │       ├── StoreCursoRequest.php
│   │       ├── UpdateCursoRequest.php
│   │       ├── StoreStudentRequest.php
│   │       └── UpdateStudentRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Curso.php
│   │   ├── Student.php
│   │   └── Registration.php
│   └── Enums/
│       └── CursoTypes.php
├── database/
│   ├── migrations/
│   ├── factories/
│   │   ├── CursoFactory.php
│   │   ├── StudentFactory.php
│   │   └── RegistrationFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── cursos/
│       ├── students/
│       ├── registrations/
│       └── layouts/
└── routes/
    └── web.php
```

## 🔍 Campos de Busca e Filtros

### Cursos
- **Busca:** Nome ou descrição
- **Filtro por tipo:** Online / Presencial
- **Ordenação:** Data de criação, Nome, Data limite
- **Direção:** Ascendente / Descendente

### Alunos
- **Busca:** Nome, Email, Telefone ou Endereço
- **Ordenação:** Data de criação, Nome, Email
- **Direção:** Ascendente / Descendente

### Matrículas
- **Busca:** Nome do aluno, Email do aluno, Nome do curso
- **Ordenação:** Data de matrícula, Aluno, Curso
- **Direção:** Ascendente / Descendente

## ✅ Validações Implementadas

### Cursos
- Nome obrigatório e único
- Tipo obrigatório
- Máximo de inscrições obrigatório (mínimo 1)
- Data limite obrigatória e no futuro

### Alunos
- Nome obrigatório
- Email obrigatório, válido e único
- Data de nascimento obrigatória e no passado
- Telefone e endereço opcionais

### Matrículas
- Aluno obrigatório
- Curso obrigatório
- Validação de vagas disponíveis
- Validação de data limite de inscrição
- Prevenção de inscrição duplicada

## 📝 Dados de Exemplo

Ao executar `./vendor/bin/sail artisan migrate:fresh --seed`, a aplicação será preenchida com:
- 2 usuários (1 admin + 1 user)
- 10 cursos variados (Online e Presencial)
- 30 alunos
- Inscrições aleatórias (cada aluno em 1-3 cursos)

## 🛠️ Comandos Úteis

```bash
# Levantar containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Executar migrations
./vendor/bin/sail artisan migrate

# Executar seeds
./vendor/bin/sail artisan db:seed

# Resetar banco (cuidado!)
./vendor/bin/sail artisan migrate:fresh --seed

# Acessar shell do container
./vendor/bin/sail shell

# Ver logs
./vendor/bin/sail logs
```

## 📄 Licença

Este projeto é de código aberto e disponível sob a licença MIT.

## 👥 Autor

Desenvolvido como solução para o desafio de matrícula de alunos em cursos.

---

**Versão:** 1.0.0  
**Data:** 19 de fevereiro de 2026
