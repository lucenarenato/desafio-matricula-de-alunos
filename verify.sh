#!/bin/bash

# Script de Verificação Rápida - Sistema de Matrícula

echo "🔍 Verificando integridade do projeto..."
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Contador
TOTAL=0
PASSED=0

# Função para verificar
check() {
    local file=$1
    local name=$2
    TOTAL=$((TOTAL + 1))

    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $name"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}✗${NC} $name - NÃO ENCONTRADO"
    fi
}

echo "📂 Verificando Controllers..."
check "app/Http/Controllers/CursoController.php" "CursoController"
check "app/Http/Controllers/StudentController.php" "StudentController"
check "app/Http/Controllers/RegistrationController.php" "RegistrationController"

echo ""
echo "📂 Verificando Models..."
check "app/Models/Curso.php" "Curso Model"
check "app/Models/Student.php" "Student Model"
check "app/Models/Registration.php" "Registration Model"

echo ""
echo "📂 Verificando Form Requests..."
check "app/Http/Requests/StoreCursoRequest.php" "StoreCursoRequest"
check "app/Http/Requests/UpdateCursoRequest.php" "UpdateCursoRequest"
check "app/Http/Requests/StoreStudentRequest.php" "StoreStudentRequest"
check "app/Http/Requests/UpdateStudentRequest.php" "UpdateStudentRequest"

echo ""
echo "📂 Verificando Middleware..."
check "app/Http/Middleware/AdminMiddleware.php" "AdminMiddleware"

echo ""
echo "📂 Verificando Factories..."
check "database/factories/CursoFactory.php" "CursoFactory"
check "database/factories/StudentFactory.php" "StudentFactory"
check "database/factories/RegistrationFactory.php" "RegistrationFactory"

echo ""
echo "📂 Verificando Migrations..."
check "database/migrations/2026_02_19_031814_create_cursos_table.php" "create_cursos_table"
check "database/migrations/2026_02_19_212929_create_students_table.php" "create_students_table"
check "database/migrations/2026_02_19_213008_create_registrations_table.php" "create_registrations_table"
check "database/migrations/2026_02_19_220000_add_role_to_users_table.php" "add_role_to_users_table"

echo ""
echo "📂 Verificando Views..."
check "resources/views/cursos/index.blade.php" "cursos/index"
check "resources/views/cursos/create.blade.php" "cursos/create"
check "resources/views/cursos/edit.blade.php" "cursos/edit"
check "resources/views/cursos/show.blade.php" "cursos/show"
check "resources/views/students/index.blade.php" "students/index"
check "resources/views/students/create.blade.php" "students/create"
check "resources/views/students/edit.blade.php" "students/edit"
check "resources/views/students/show.blade.php" "students/show"
check "resources/views/registrations/index.blade.php" "registrations/index"
check "resources/views/registrations/create.blade.php" "registrations/create"

echo ""
echo "📂 Verificando Documentação..."
check "RESUMO_EXECUTIVO.md" "RESUMO_EXECUTIVO.md"
check "COMPLETO.md" "COMPLETO.md"
check "TESTES.md" "TESTES.md"

echo ""
echo "=================================================="
echo -e "✅ ${GREEN}$PASSED${NC} de ${YELLOW}$TOTAL${NC} arquivos verificados com sucesso!"
echo "=================================================="

if [ $PASSED -eq $TOTAL ]; then
    echo -e "${GREEN}✨ Projeto completo e pronto para uso!${NC}"
    exit 0
else
    echo -e "${RED}⚠️  Alguns arquivos estão faltando!${NC}"
    exit 1
fi
