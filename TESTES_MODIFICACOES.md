# Recomendações de Ajustes nos Testes

## ✅ Testes que NÃO precisam de ajuste

Os testes existentes para CRUD de Cursos e Registrations continuam válidos:
- `test_index_is_accessible_by_admin` - Admin ainda pode ver listagem
- `test_create_is_accessible_by_admin` - Admin pode criar cursos
- `test_store_creates_curso` - Criação de cursos continua funcionando
- `test_destroy_deletes_curso` - Deletar curso continua funcionando
- `test_prevent_duplicate_enrollment` - Validação de duplicação ainda funciona
- E outros testes de CRUD

## ⚠️ Testes que PRECISAM ser adicionados

### 1. **Teste para `CursoController::list()`**
```php
public function test_list_is_accessible_by_authenticated_user(): void
{
    $response = $this->actingAs($this->user)->get(route('cursos.list'));
    $response->assertOk();
    $response->assertViewIs('cursos.list');
}

public function test_list_requires_authentication(): void
{
    $response = $this->get(route('cursos.list'));
    $response->assertRedirect('login');
}
```

### 2. **Teste para `CursoController::enroll()`**
```php
public function test_user_can_enroll_in_course(): void
{
    $curso = Curso::factory()->create();
    $response = $this->actingAs($this->user)->post(route('cursos.enroll', $curso));
    
    $this->assertDatabaseHas('registrations', [
        'user_id' => $this->user->id,
        'cursos_id' => $curso->id,
    ]);
    $response->assertRedirect(route('cursos.list'));
}

public function test_prevent_duplicate_enrollment_for_users(): void
{
    $curso = Curso::factory()->create();
    
    // First enrollment
    $this->actingAs($this->user)->post(route('cursos.enroll', $curso));
    
    // Attempt duplicate
    $response = $this->actingAs($this->user)->post(route('cursos.enroll', $curso));
    $response->assertSessionHasErrors(); // ou verifica a mensagem
}

public function test_cannot_enroll_when_course_is_full(): void
{
    $curso = Curso::factory()->create(['maximum_enrollments' => 1]);
    $student = Student::factory()->create();
    
    // Fill course with Student
    Registration::factory()->create([
        'students_id' => $student->id,
        'cursos_id' => $curso->id,
    ]);
    
    $response = $this->actingAs($this->user)->post(route('cursos.enroll', $curso));
    $response->assertSessionHasErrors();
}

public function test_cannot_enroll_after_deadline(): void
{
    $curso = Curso::factory()->create(['registration_deadline' => now()->subDay()]);
    $response = $this->actingAs($this->user)->post(route('cursos.enroll', $curso));
    $response->assertSessionHasErrors();
}
```

### 3. **Teste para `RegistrationController::my()`**
```php
public function test_user_can_view_their_enrollments(): void
{
    $curso = Curso::factory()->create();
    
    // Create enrollment with user_id
    Registration::factory()->create([
        'user_id' => $this->user->id,
        'cursos_id' => $curso->id,
    ]);
    
    $response = $this->actingAs($this->user)->get(route('registrations.my'));
    $response->assertOk();
    $response->assertViewIs('registrations.my');
    $response->assertViewHas('registrations');
}

public function test_user_sees_only_their_enrollments(): void
{
    $otherUser = User::factory()->create(['role' => 'user']);
    $curso = Curso::factory()->create();
    
    Registration::factory()->create([
        'user_id' => $otherUser->id,
        'cursos_id' => $curso->id,
    ]);
    
    $response = $this->actingAs($this->user)->get(route('registrations.my'));
    $registrations = $response->viewData('registrations');
    
    $this->assertEquals(0, $registrations->count());
}
```

### 4. **Teste para `RegistrationController::cancel()`**
```php
public function test_user_can_cancel_their_enrollment(): void
{
    $curso = Curso::factory()->create();
    
    $registration = Registration::factory()->create([
        'user_id' => $this->user->id,
        'cursos_id' => $curso->id,
    ]);
    
    $response = $this->actingAs($this->user)->delete(route('registrations.cancel', $registration));
    
    $this->assertDatabaseMissing('registrations', ['id' => $registration->id]);
    $response->assertRedirect(route('registrations.my'));
}

public function test_user_cannot_cancel_others_enrollment(): void
{
    $otherUser = User::factory()->create(['role' => 'user']);
    $curso = Curso::factory()->create();
    
    $registration = Registration::factory()->create([
        'user_id' => $otherUser->id,
        'cursos_id' => $curso->id,
    ]);
    
    $response = $this->actingAs($this->user)->delete(route('registrations.cancel', $registration));
    
    $this->assertDatabaseHas('registrations', ['id' => $registration->id]);
    $response->assertSessionHasErrors();
}
```

## 📝 Observações Importantes

1. **Observer**: O ajuste no `RegistrationObserver` funciona tanto com `user_id` quanto com `students_id`, então os testes existentes devem continuar funcionando.

2. **Testes de Validação**: Alguns testes como `test_prevent_duplicate_enrollment` testam a validação da request, mas agora a lógica de validação está no controller. Você pode manter os testes existentes E adicionar novos para la lógica do controller.

3. **Factories**: Verifique se as factories estão criando registros corretamente com `user_id` quando necessário.

4. **Migration**: A migration que adiciona `user_id` é necessária e já foi executada.

## ✨ Recomendação

Adicione testes para as 4 novas funcionalidades (list, enroll, my, cancel) aos respectivos arquivos de teste para garantir que tudo funciona conforme esperado.
