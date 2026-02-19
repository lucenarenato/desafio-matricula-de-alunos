# API REST - Documentação Completa

## 🔐 Autenticação

A API usa **JWT (JSON Web Token)** para autenticação. Todos os endpoints (exceto login) requerem um token válido.

### 1. Login

**Endpoint**: `POST /api/auth/login`

**Request**:
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  },
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### 2. Usando o Token

Inclua o token em todas as requisições subsequentes:

```bash
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### 3. Refresh Token

**Endpoint**: `POST /api/auth/refresh`

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200 OK):
```json
{
  "success": true,
  "access_token": "novo_token...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

### 4. Get Current User

**Endpoint**: `GET /api/auth/me`

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200 OK):
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "role": "admin"
  }
}
```

### 5. Logout

**Endpoint**: `POST /api/auth/logout`

**Headers**:
```
Authorization: Bearer {token}
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Logout realizado com sucesso"
}
```

---

## 📚 CRUD - Cursos

### List Cursos

**Endpoint**: `GET /api/cursos`

**Query Parameters**:
- `page` (int, default: 1) - Página
- `per_page` (int, default: 15) - Itens por página (5, 10, 15, 25, 50, 100)
- `search` (string) - Buscar por nome ou descrição
- `type` (string) - Filtrar por tipo (On-line, Presencial)
- `sort_by` (string, default: created_at) - Campo de ordenação
- `sort_order` (string, default: desc) - Ordem (asc, desc)

**Example**:
```bash
GET /api/cursos?page=1&per_page=10&search=PHP&type=On-line&sort_by=name&sort_order=asc
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Cursos listados com sucesso",
  "data": [
    {
      "id": 1,
      "name": "PHP Avançado",
      "description": "Curso de PHP...",
      "type": "On-line",
      "maximum_enrollments": 30,
      "registration_deadline": "2026-03-20T10:00:00.000000Z",
      "created_at": "2026-02-19T18:00:00.000000Z",
      "updated_at": "2026-02-19T18:00:00.000000Z"
    }
  ],
  "pagination": {
    "total": 25,
    "per_page": 10,
    "current_page": 1,
    "last_page": 3,
    "from": 1,
    "to": 10
  }
}
```

### Create Curso

**Endpoint**: `POST /api/cursos`

**Request Body**:
```json
{
  "name": "Laravel 11",
  "description": "Curso avançado de Laravel 11",
  "type": "On-line",
  "maximum_enrollments": 40,
  "registration_deadline": "2026-03-20T10:00:00Z"
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "message": "Curso criado com sucesso",
  "data": {
    "id": 25,
    "name": "Laravel 11",
    "description": "Curso avançado de Laravel 11",
    "type": "On-line",
    "maximum_enrollments": 40,
    "registration_deadline": "2026-03-20T10:00:00.000000Z",
    "created_at": "2026-02-19T18:00:00.000000Z",
    "updated_at": "2026-02-19T18:00:00.000000Z"
  }
}
```

### Get Curso

**Endpoint**: `GET /api/cursos/{id}`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "PHP Avançado",
    "description": "Curso de PHP...",
    "type": "On-line",
    "maximum_enrollments": 30,
    "registration_deadline": "2026-03-20T10:00:00.000000Z",
    "registrations": [],
    "students": []
  }
}
```

### Update Curso

**Endpoint**: `PUT /api/cursos/{id}`

**Request Body**:
```json
{
  "name": "PHP Avançado - Atualizado",
  "description": "Descrição atualizada",
  "type": "Presencial",
  "maximum_enrollments": 50,
  "registration_deadline": "2026-04-20T10:00:00Z"
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Curso atualizado com sucesso",
  "data": { ... }
}
```

### Delete Curso

**Endpoint**: `DELETE /api/cursos/{id}`

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Curso deletado com sucesso"
}
```

### Bulk Delete Cursos

**Endpoint**: `POST /api/cursos/bulk-delete`

**Request Body**:
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "5 curso(s) deletado(s) com sucesso",
  "deleted_count": 5
}
```

---

## 👨‍🎓 CRUD - Alunos (Students)

### List Students

**Endpoint**: `GET /api/students`

**Query Parameters**:
- `page` (int, default: 1)
- `per_page` (int, default: 15)
- `search` (string) - Buscar por nome, email, telefone
- `sort_by` (string, default: created_at)
- `sort_order` (string, default: desc)

**Example**:
```bash
GET /api/students?page=1&per_page=20&search=João
```

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Alunos listados com sucesso",
  "data": [
    {
      "id": 1,
      "name": "João Silva",
      "email": "joao@example.com",
      "date_of_birth": "1995-05-15",
      "phone": "11999999999",
      "address": "Rua Teste, 123",
      "created_at": "2026-02-19T18:00:00.000000Z",
      "updated_at": "2026-02-19T18:00:00.000000Z"
    }
  ],
  "pagination": { ... }
}
```

### Create Student

**Endpoint**: `POST /api/students`

**Request Body**:
```json
{
  "name": "Maria Santos",
  "email": "maria@example.com",
  "date_of_birth": "1998-08-20",
  "phone": "11988888888",
  "address": "Av. Principal, 456"
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "message": "Aluno criado com sucesso",
  "data": { ... }
}
```

### Get Student

**Endpoint**: `GET /api/students/{id}`

**Response** (200 OK):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "date_of_birth": "1995-05-15",
    "phone": "11999999999",
    "address": "Rua Teste, 123",
    "registrations": [],
    "cursos": []
  }
}
```

### Update Student

**Endpoint**: `PUT /api/students/{id}`

**Request Body**:
```json
{
  "name": "João Silva Atualizado",
  "email": "joao.novo@example.com",
  "date_of_birth": "1995-05-15",
  "phone": "11987654321",
  "address": "Nova Rua, 789"
}
```

### Delete Student

**Endpoint**: `DELETE /api/students/{id}`

### Bulk Delete Students

**Endpoint**: `POST /api/students/bulk-delete`

**Request Body**:
```json
{
  "ids": [1, 2, 3]
}
```

---

## 📝 CRUD - Matrículas (Registrations)

### List Registrations

**Endpoint**: `GET /api/registrations`

**Query Parameters**:
- `page` (int, default: 1)
- `per_page` (int, default: 15)
- `search` (string) - Buscar por nome do aluno ou curso
- `sort_by` (string, default: created_at)
- `sort_order` (string, default: desc)

**Response** (200 OK):
```json
{
  "success": true,
  "message": "Matrículas listadas com sucesso",
  "data": [
    {
      "id": 1,
      "students_id": 1,
      "cursos_id": 1,
      "student": { ... },
      "curso": { ... },
      "created_at": "2026-02-19T18:00:00.000000Z",
      "updated_at": "2026-02-19T18:00:00.000000Z"
    }
  ],
  "pagination": { ... }
}
```

### Create Registration (Enroll Student)

**Endpoint**: `POST /api/registrations`

**Request Body**:
```json
{
  "students_id": 5,
  "cursos_id": 3
}
```

**Response** (201 Created):
```json
{
  "success": true,
  "message": "Matrícula criada com sucesso",
  "data": {
    "id": 45,
    "students_id": 5,
    "cursos_id": 3,
    "created_at": "2026-02-19T18:00:00.000000Z",
    "updated_at": "2026-02-19T18:00:00.000000Z"
  }
}
```

**Error Responses**:

Duplicate enrollment (409 Conflict):
```json
{
  "success": false,
  "message": "Este aluno já está inscrito neste curso!"
}
```

Course full (409 Conflict):
```json
{
  "success": false,
  "message": "Este curso não possui mais vagas disponíveis!"
}
```

Registration deadline passed (409 Conflict):
```json
{
  "success": false,
  "message": "O período de inscrição para este curso foi encerrado!"
}
```

### Get Registration

**Endpoint**: `GET /api/registrations/{id}`

### Delete Registration

**Endpoint**: `DELETE /api/registrations/{id}`

### Bulk Delete Registrations

**Endpoint**: `POST /api/registrations/bulk-delete`

**Request Body**:
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

---

## 🧪 Testando a API com cURL

### Login
```bash
curl -X POST http://localhost:80/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### List Cursos (com token)
```bash
curl -X GET http://localhost:80/api/cursos?per_page=10 \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### Create Curso
```bash
curl -X POST http://localhost:80/api/cursos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{
    "name": "Novo Curso",
    "description": "Descrição",
    "type": "On-line",
    "maximum_enrollments": 30,
    "registration_deadline": "2026-03-20T10:00:00Z"
  }'
```

### Bulk Delete
```bash
curl -X POST http://localhost:80/api/cursos/bulk-delete \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{
    "ids": [1, 2, 3]
  }'
```

---

## 📊 HTTP Status Codes

| Status | Significado |
|--------|-------------|
| 200 | OK - Requisição bem-sucedida |
| 201 | Created - Recurso criado com sucesso |
| 401 | Unauthorized - Token inválido ou ausente |
| 409 | Conflict - Validação de negócio falhou |
| 422 | Unprocessable Entity - Erro de validação |
| 500 | Internal Server Error - Erro do servidor |

---

## 🔒 Segurança

- ✅ JWT tokens com expiração de 1 hora
- ✅ Validação de todos os inputs
- ✅ Proteção CSRF na web
- ✅ Roles (admin/user) para autorização
- ✅ Cascade delete para integridade de dados

---

## 📱 Exemplo Completo com JavaScript/Fetch

```javascript
// 1. Login
const loginResponse = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'admin@example.com',
    password: 'password'
  })
});

const { access_token } = await loginResponse.json();

// 2. Listar cursos com paginação
const cursosResponse = await fetch('/api/cursos?page=1&per_page=10', {
  headers: { 'Authorization': `Bearer ${access_token}` }
});

const cursosData = await cursosResponse.json();
console.log(cursosData.data);

// 3. Criar novo curso
const newCurso = await fetch('/api/cursos', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${access_token}`
  },
  body: JSON.stringify({
    name: 'Laravel 11',
    type: 'On-line',
    maximum_enrollments: 40,
    registration_deadline: '2026-03-20T10:00:00Z'
  })
});

// 4. Deletar em massa
const bulkDelete = await fetch('/api/cursos/bulk-delete', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${access_token}`
  },
  body: JSON.stringify({
    ids: [1, 2, 3, 4, 5]
  })
});

// 5. Logout
await fetch('/api/auth/logout', {
  method: 'POST',
  headers: { 'Authorization': `Bearer ${access_token}` }
});
```

---

**Última atualização**: 19 de Fevereiro de 2026
**Versão da API**: 1.0
