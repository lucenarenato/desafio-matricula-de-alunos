# Guia de Testes - Sistema de Matrícula

## 🧪 Testes Manuais Recomendados

### 1. Autenticação
- [ ] Fazer login com admin@example.com / password
- [ ] Fazer login com user@example.com / password
- [ ] Verificar que usuário comum é redirecionado quando tenta acessar admin
- [ ] Logout funciona corretamente

### 2. CRUD de Cursos

#### Listar Cursos
- [ ] Acessar /cursos (página index)
- [ ] Verificar listagem de 15 itens por página
- [ ] Paginação funciona
- [ ] Links de navegação (Próxima, Anterior) funcionam

#### Buscar/Filtrar Cursos
- [ ] Filtrar por nome - retorna resultados corretos
- [ ] Filtrar por tipo (Online/Presencial)
- [ ] Ordenar por Nome em ordem ascendente
- [ ] Ordenar por Nome em ordem descendente
- [ ] Ordenar por Data Limite
- [ ] Combinações de filtros funcionam

#### Criar Curso
- [ ] Acessar /cursos/create
- [ ] Preencher todos os campos corretamente
- [ ] Validação: Nome vazio mostra erro
- [ ] Validação: Nome duplicado mostra erro
- [ ] Validação: Data limite no passado mostra erro
- [ ] Validação: Máximo de inscrições < 1 mostra erro
- [ ] Curso criado com sucesso aparece na lista
- [ ] Mensagem de sucesso exibida

#### Editar Curso
- [ ] Acessar edição de um curso
- [ ] Modificar dados
- [ ] Validações funcionam na edição
- [ ] Curso atualizado corretamente
- [ ] Mensagem de sucesso exibida

#### Visualizar Detalhes do Curso
- [ ] Acessar /cursos/{id}
- [ ] Todos os dados do curso aparecem corretamente
- [ ] Lista de alunos inscritos aparece
- [ ] Status do curso (Aberto/Lotado/Fechado) correto
- [ ] Número de vagas disponíveis correto

#### Deletar Curso
- [ ] Confirmar deleção com diálogo
- [ ] Curso removido da listagem
- [ ] Mensagem de sucesso exibida

### 3. CRUD de Alunos

#### Listar Alunos
- [ ] Acessar /students (página index)
- [ ] Verificar listagem de 15 itens por página
- [ ] Paginação funciona
- [ ] Todos os dados visíveis (nome, email, telefone, data nascimento)

#### Buscar/Filtrar Alunos
- [ ] Filtrar por nome - retorna resultados corretos
- [ ] Filtrar por email - retorna resultados corretos
- [ ] Filtrar por telefone - retorna resultados corretos
- [ ] Ordenar por campos diferentes funciona

#### Criar Aluno
- [ ] Acessar /students/create
- [ ] Validação: Email vazio mostra erro
- [ ] Validação: Email inválido mostra erro
- [ ] Validação: Email duplicado mostra erro
- [ ] Validação: Data nascimento no futuro mostra erro
- [ ] Aluno criado com sucesso

#### Editar Aluno
- [ ] Modificar dados do aluno
- [ ] Validações funcionam
- [ ] Email único (não pode duplicar com outro aluno)

#### Visualizar Detalhes do Aluno
- [ ] Todos os dados do aluno aparecem
- [ ] Lista de cursos inscritos aparece
- [ ] Possibilidade de cancelar matrícula

#### Deletar Aluno
- [ ] Aluno removido com sucesso
- [ ] Matrículas do aluno são removidas em cascata

### 4. CRUD de Matrículas

#### Listar Matrículas
- [ ] Ver todas as matrículas ativas
- [ ] Paginação de 15 itens funciona
- [ ] Informações do aluno e curso aparecem

#### Buscar Matrículas
- [ ] Buscar por nome do aluno
- [ ] Buscar por email do aluno
- [ ] Buscar por nome do curso

#### Criar Matrícula
- [ ] Acessar /registrations/create
- [ ] Selecionar aluno e curso
- [ ] ✅ Validação: Impedir inscrição duplicada
- [ ] ✅ Validação: Curso sem vagas disponíveis
- [ ] ✅ Validação: Período de inscrição encerrado
- [ ] Matrícula criada com sucesso

#### Cancelar Matrícula
- [ ] Matrícula removida com sucesso
- [ ] Mensagem de confirmação funciona

### 5. Relacionamentos e Integridade

#### Aluno → Cursos
- [ ] Aluno pode estar inscrito em vários cursos
- [ ] Visualizar todos os cursos de um aluno
- [ ] Contar e exibir correto número de inscrições

#### Curso → Alunos
- [ ] Curso pode ter vários alunos inscritos
- [ ] Visualizar lista de alunos do curso
- [ ] Contar e exibir número correto de vagas

#### Proteção de Dados
- [ ] Deletar aluno remove suas matrículas
- [ ] Deletar curso remove suas matrículas
- [ ] Integridade referencial mantida

### 6. Permissões e Middleware

#### Acesso Admin
- [ ] Admin vê menu de navegação (Cursos, Alunos, Matrículas)
- [ ] Admin pode acessar /cursos
- [ ] Admin pode acessar /students
- [ ] Admin pode acessar /registrations

#### Acesso Usuário Comum
- [ ] Usuário comum NÃO vê menu administrativo
- [ ] Usuário comum redirecionado ao acessar /cursos
- [ ] Usuário comum redirecionado ao acessar /students
- [ ] Usuário comum redirecionado ao acessar /registrations
- [ ] Mensagem de erro apropriada exibida

### 7. UI/UX

- [ ] Todas as páginas têm visual consistente
- [ ] Dark mode funciona (se implementado)
- [ ] Botões têm hover effects
- [ ] Formulários têm validação visual
- [ ] Mensagens de sucesso/erro bem visíveis
- [ ] Tabelas são responsivas

## 🐛 Testes de Casos Extremos

- [ ] Criar matrícula quando curso tem 1 vaga e tenta inscrever 2 alunos
- [ ] Data limite passada não permite inscrições
- [ ] Mesmo aluno não pode se inscrever 2x no mesmo curso
- [ ] Editar data limite do curso muda validações
- [ ] Deletar curso with alunos inscritos

## ✅ Checklist Final

- [ ] Todas as migrations executam sem erro
- [ ] Seeds populam dados corretamente
- [ ] Aplicação inicia sem erros
- [ ] Nenhum erro no console do navegador
- [ ] Nenhum erro nos logs do Laravel
- [ ] Paginação está em 15 itens conforme requisitado
- [ ] Todos os requisitos foram implementados

## 📊 Dados para Testes

Após rodar `./vendor/bin/sail artisan migrate:fresh --seed`:

- **Usuários:** 2 (1 admin + 1 user)
- **Cursos:** 10
- **Alunos:** 30
- **Matrículas:** ~30-90 (aleatórias, 1-3 por aluno)

---

**Data:** 19 de fevereiro de 2026
