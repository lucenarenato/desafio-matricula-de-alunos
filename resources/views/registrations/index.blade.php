<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Matrículas') }}
            </h2>
            <a href="{{ route('registrations.create') }}"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nova Matrícula
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulário de Busca e Filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('registrations.index') }}" method="GET" class="flex gap-4 flex-wrap">
                        <input type="text" name="search" placeholder="Buscar por aluno ou curso..."
                            value="{{ $search }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                        <select name="sort_by"
                            class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <option value="created_at" {{ $sort_by === 'created_at' ? 'selected' : '' }}>Data de
                                Matrícula</option>
                            <option value="students_id" {{ $sort_by === 'students_id' ? 'selected' : '' }}>Aluno
                            </option>
                            <option value="cursos_id" {{ $sort_by === 'cursos_id' ? 'selected' : '' }}>Curso</option>
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

            <!-- Tabela de Matrículas -->
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
                                    Aluno</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Email Aluno</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Curso</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Tipo de Curso</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Data Matrícula</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">
                                        <input type="checkbox"
                                            class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                            value="{{ $registration->id }}" onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->student->name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->student->email }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->curso->name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        <span
                                            class="px-3 py-1 text-sm rounded-full {{ $registration->curso->type === 'Online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $registration->curso->type === 'Online' ? 'Online' : 'Presencial' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('registrations.destroy', $registration) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza?')">Cancelar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $registrations->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Form oculto para bulk delete -->
    <form id="bulk-delete-form" action="{{ route('registrations.bulkDelete') }}" method="POST" style="display: none;">
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

        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Selecione pelo menos um item para deletar.');
                return;
            }

            if (!confirm(`Tem certeza que deseja deletar ${checkboxes.length} item(ns) selecionado(s)?`)) {
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
            document.getElementById('bulk-delete-ids').value = ids;
            document.getElementById('bulk-delete-form').submit();
        }
    </script>
</x-app-layout>
