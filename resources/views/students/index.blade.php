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
                        <button type="submit"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Filtrar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabela de Alunos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
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
                                        <form action="{{ route('students.destroy', $student) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Tem certeza?')">Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
