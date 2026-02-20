<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Cursos') }}
            </h2>
            <a href="{{ route('cursos.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Novo Curso
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulário de Busca e Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('cursos.index') }}" method="GET" class="flex gap-4 flex-wrap">
                        <input type="text" name="search" placeholder="Buscar por nome ou descrição..."
                            value="{{ $search }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                        <select name="type"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="">Todos os tipos</option>
                            <option value="Online" {{ $type === 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="InPerson" {{ $type === 'InPerson' ? 'selected' : '' }}>Presencial</option>
                        </select>
                        <select name="sort_by"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="created_at" {{ $sort_by === 'created_at' ? 'selected' : '' }}>Data de Criação
                            </option>
                            <option value="name" {{ $sort_by === 'name' ? 'selected' : '' }}>Nome</option>
                            <option value="registration_deadline"
                                {{ $sort_by === 'registration_deadline' ? 'selected' : '' }}>Data Limite</option>
                        </select>
                        <select name="sort_order"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="desc" {{ $sort_order === 'desc' ? 'selected' : '' }}>Descendente</option>
                            <option value="asc" {{ $sort_order === 'asc' ? 'selected' : '' }}>Ascendente</option>
                        </select>
                        <select name="per_page" onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10 por página
                            </option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25 por página
                            </option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50 por página
                            </option>
                            <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100 por
                                página</option>
                        </select>
                        <button type="submit"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Filtrar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Botão de Deleção em Massa (apenas para admins) -->
            @if (auth()->user() && auth()->user()->isAdmin())
                <div class="mb-4">
                    <button type="button" id="bulk-delete-btn" onclick="bulkDelete()"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Deletar Selecionados (<span id="selected-count">0</span>)
                    </button>
                </div>
            @endif

            <!-- Tabela de Cursos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                @if (auth()->user() && auth()->user()->isAdmin())
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                    </th>
                                @endif
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Nome</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Tipo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Inscritos</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Vagas</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Data Limite</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cursos as $curso)
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    @if (auth()->user() && auth()->user()->isAdmin())
                                        <td class="px-6 py-4">
                                            <input type="checkbox"
                                                class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                                value="{{ $curso->id }}" onchange="updateBulkDeleteButton()">
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $curso->name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        <span
                                            class="px-3 py-1 text-sm rounded-full {{ $curso->type === 'Online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $curso->type === 'Online' ? 'Online' : 'Presencial' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $curso->registrations()->count() }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $curso->maximum_enrollments - $curso->registrations()->count() }}/{{ $curso->maximum_enrollments }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $curso->registration_deadline->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('cursos.show', $curso) }}"
                                            class="text-blue-600 hover:text-blue-900">Detalhes</a>
                                        @if (auth()->user() && auth()->user()->isAdmin())
                                            <a href="{{ route('cursos.edit', $curso) }}"
                                                class="text-green-600 hover:text-green-900">Editar</a>
                                            <button type="button" class="text-red-600 hover:text-red-900"
                                                onclick="confirmDelete('{{ route('cursos.destroy', $curso) }}', 'curso')">Deletar</button>
                                        @elseif(auth()->user() && auth()->user()->isUser())
                                            @if (auth()->user()->registrations()->where('cursos_id', $curso->id)->exists())
                                                <span class="text-gray-600">Matriculado</span>
                                            @else
                                                <form method="POST" action="{{ route('cursos.enroll', $curso) }}"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-green-600 hover:text-green-900">Matricular</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $cursos->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user() && auth()->user()->isAdmin())
        <!-- Form oculto para bulk delete -->
        <form id="bulk-delete-form" action="{{ route('cursos.bulkDelete') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="ids" id="bulk-delete-ids">
        </form>

        <script>
            function toggleSelectAll(checkbox) {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = checkbox.checked;
                });
                updateBulkDeleteButton();
            }

            function updateBulkDeleteButton() {
                const checkboxes = document.querySelectorAll('.item-checkbox:checked');
                const count = checkboxes.length;
                const btn = document.getElementById('bulk-delete-btn');
                const countSpan = document.getElementById('selected-count');

                countSpan.textContent = count;
                btn.disabled = count === 0;

                // Update "select all" checkbox state
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const selectAllCheckbox = document.getElementById('select-all');
                selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            }

            function confirmDelete(url, type) {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: `Deseja realmente deletar este ${type}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, deletar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;
                        form.innerHTML = `
                            @csrf
                            @method('DELETE')
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function bulkDelete() {
                const checkboxes = document.querySelectorAll('.item-checkbox:checked');
                if (checkboxes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção',
                        text: 'Selecione pelo menos um item para deletar.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Tem certeza?',
                    text: `Deseja realmente deletar ${checkboxes.length} item(ns) selecionado(s)?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sim, deletar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
                        document.getElementById('bulk-delete-ids').value = ids;
                        document.getElementById('bulk-delete-form').submit();
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
