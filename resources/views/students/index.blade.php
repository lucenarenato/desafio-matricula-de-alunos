<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Alunos') }}
            </h2>
            <a href="{{ route('students.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Novo Aluno
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulário de Busca e Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('students.index') }}" method="GET" class="flex gap-4 flex-wrap">
                        <input type="text" name="search" placeholder="Buscar por nome, email ou telefone..."
                            value="{{ $search }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                        <select name="sort_by"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="created_at" {{ $sort_by === 'created_at' ? 'selected' : '' }}>Data de Criação
                            </option>
                            <option value="name" {{ $sort_by === 'name' ? 'selected' : '' }}>Nome</option>
                            <option value="email" {{ $sort_by === 'email' ? 'selected' : '' }}>Email</option>
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

            <!-- Botão de Deleção em Massa -->
            <div class="mb-4">
                <button type="button" id="bulk-delete-btn" onclick="bulkDelete()"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Deletar Selecionados (<span id="selected-count">0</span>)
                </button>
            </div>

            <!-- Tabela de Alunos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Nome</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Telefone</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Data Nascimento</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">
                                        <input type="checkbox"
                                            class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                            value="{{ $student->id }}" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $student->name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $student->email }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $student->phone ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $student->date_of_birth->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('students.show', $student) }}"
                                            class="text-blue-600 hover:text-blue-900">Detalhes</a>
                                        <a href="{{ route('students.edit', $student) }}"
                                            class="text-green-600 hover:text-green-900">Editar</a>
                                        <button type="button" class="text-red-600 hover:text-red-900"
                                            onclick="confirmDelete('{{ route('students.destroy', $student) }}', 'aluno')">Deletar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $students->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Form oculto para bulk delete -->
    <form id="bulk-delete-form" action="{{ route('students.bulkDelete') }}" method="POST" style="display: none;">
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
</x-app-layout>
