# 🎓 Sistema de Matrícula - Guia Rápido de Início

## 🚀 Iniciar em 3 Passos

### 1️⃣ Levante os containers Docker
```bash
cd "/home/renato/Downloads/code php/laravel11-teste-suit"
./vendor/bin/sail up -d
```

### 2️⃣ Execute as migrations e seeds
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### 3️⃣ Abra no navegador
```
http://localhost
```

---

## 📝 Credenciais de Acesso

| Tipo | Email | Senha |
|------|-------|-------|
| **Admin** | admin@example.com | password |
| **User** | user@example.com | password |

> Faça login como **admin** para acessar o painel administrativo

---

## ✨ O que é Possível Fazer

### 📚 Cursos
- ✅ Criar novos cursos (Online ou Presencial)
- ✅ Editar informações dos cursos
- ✅ Listar com busca, filtros e paginação
- ✅ Ver alunos inscritos em cada curso
- ✅ Deletar cursos

### 👥 Alunos
- ✅ Criar novos alunos
- ✅ Editar informações dos alunos
- ✅ Listar com busca e paginação
- ✅ Ver cursos que aluno está inscrito
- ✅ Deletar alunos

### 📋 Matrículas
- ✅ Criar novas matrículas
- ✅ Listar todas as matrículas com busca
- ✅ Cancelar matrículas
- ✅ Sistema inteligente que:
  - Impede inscrição duplicada
  - Valida vagas disponíveis
  - Valida data limite de inscrição

---

## 📊 Dados Pré-carregados

Após rodar `migrate:fresh --seed`, você terá:

- **2 usuários:** 1 admin + 1 comum
- **10 cursos:** Variados (Online e Presencial)
- **30 alunos:** Com informações completas
- **~50 matrículas:** Aleatórias para teste

---

## 🛠️ Comandos Úteis

```bash
# Ver logs em tempo real
./vendor/bin/sail logs -f

# Acessar shell do container
./vendor/bin/sail shell

# Parar containers
./vendor/bin/sail down

# Limpar cache
./vendor/bin/sail artisan cache:clear

# Resetar banco (cuidado!)
./vendor/bin/sail artisan migrate:fresh --seed

# Rodar apenas seeds
./vendor/bin/sail artisan db:seed

# Verificar integridade do projeto
./verify.sh
```

---

## 🎨 Interfaces Disponíveis

### Admin Dashboard
- Menu de navegação com acesso a: Cursos, Alunos, Matrículas
- Cada seção tem tabelas com filtros avançados
- Formulários para CRUD de operações
- Status visual (cores) para diferentes tipos

### Features UI
- ✅ **Dark Mode Ready** - Tailwind CSS theme
- ✅ **Responsivo** - Funciona em desktop e mobile
- ✅ **Validações em Tempo Real** - Feedback imediato
- ✅ **Confirmação de Ações** - Diálogos para deletar
- ✅ **Mensagens de Status** - Sucesso/erro bem visíveis

---

## 📚 Documentação Completa

- **[RESUMO_EXECUTIVO.md](RESUMO_EXECUTIVO.md)** - Vision geral do projeto
- **[COMPLETO.md](COMPLETO.md)** - Documentação técnica detalhada
- **[TESTES.md](TESTES.md)** - Checklist de testes manual
- **[README.md](README.md)** - Instrições do Laravel

---

## 🔒 Segurança

O projeto implementa:
- ✅ CSRF Protection (padrão Laravel)
- ✅ Validação de Autorização (Middleware Admin)
- ✅ Validação de Dados (Form Requests)
- ✅ Sanitização (Blade escape automático)
- ✅ Hashing de Senha (Laravel Hash)

---

## 🐛 Resolver Problemas

### Porta 80 já em uso?
```bash
# Alterar porta no docker-compose.yml
# Mudar: ports: - "80:80"
# Para: ports: - "8000:80"
```

### Erro ao executar migrate?
```bash
# Verificar logs
./vendor/bin/sail logs mysql

# Resetar banco
./vendor/bin/sail artisan migrate:fresh --seed
```

### Banco não conecta?
```bash
# Reiniciar containers
./vendor/bin/sail down
./vendor/bin/sail up -d

# Esperar ~30 segundos MySQL subir
./vendor/bin/sail artisan migrate
```

---

## 📱 Acesso Remoto

Se quiser acessar de outro computador:

1. Descobrir IP da máquina
```bash
hostname -I
# ex: 192.168.1.100
```

2. Alterar em `docker-compose.yml` a porta
```yaml
ports:
  - "192.168.1.100:80:80"
```

3. Acessar
```
http://192.168.1.100
```

---

## ✅ Checklist de Setup

- [ ] Clone/baixe o repositório
- [ ] Docker e Docker Compose instalados
- [ ] Rode `./vendor/bin/sail up -d`
- [ ] Rode `./vendor/bin/sail artisan migrate:fresh --seed`
- [ ] Abra http://localhost no navegador
- [ ] Faça login com admin@example.com / password
- [ ] Explore os cursos, alunos e matrículas
- [ ] Rode `./verify.sh` para validar integridade

---

## 🎯 Próximos Passos (Opcional)

1. **Customizar dados de seed** - Edite `database/seeders/DatabaseSeeder.php`
2. **Adicionar mais validações** - Edite as Form Requests
3. **Mudar cores** - Modifique o `tailwind.config.js`
4. **Adicionar notificações por email** - Use `Mail` do Laravel
5. **Exportar para PDF** - Use a lib `barryvdh/laravel-dompdf`
6. **Autenticação 2FA** - Use `pragmarx/google2fa`

---

## 📞 Suporte

Caso encontre algum problema:

1. Verifique os logs: `./vendor/bin/sail logs`
2. Resetaque o banco: `./vendor/bin/sail artisan migrate:fresh --seed`
3. Limpe o cache: `./vendor/bin/sail artisan cache:clear`
4. Verifique a integridade: `./verify.sh`

---

## 📈 Performance

O sistema foi otimizado para:
- ✅ Queries otimizadas com Eager Loading
- ✅ Paginação eficiente (15 itens)
- ✅ Índices nas Foreign Keys
- ✅ Cache de configurações

---

**Versão:** 1.0.0  
**Status:** ✅ Pronto para Produção  
**Data:** 19 de fevereiro de 2026

---

Aproveite! 🚀
