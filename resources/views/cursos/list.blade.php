<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cursos Disponíveis para Matrícula') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($cursos->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __('Nenhum curso disponível para matrícula no momento.') }}
                    </div>
                </div>
            @else
                <!-- Formulário de Busca e Filtros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form action="{{ route('cursos.list') }}" method="GET" class="flex gap-4 flex-wrap">
                            <input type="text" name="search" placeholder="Buscar por nome ou descrição..."
                                value="{{ $search }}"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                            <select name="type"
                                class="px-4 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Todos os tipos</option>
                                <option value="Online" {{ $type === 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="InPerson" {{ $type === 'InPerson' ? 'selected' : '' }}>Presencial
                                </option>
                            </select>
                            <button type="submit"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Filtrar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Grid de Cursos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($cursos as $curso)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $curso->name }}
                                </h3>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    {{ Str::limit($curso->description, 100) }}
                                </p>

                                <div class="space-y-2 mb-4 text-sm text-gray-700 dark:text-gray-300">
                                    <p>
                                        <strong>Tipo:</strong>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $curso->type === 'Online' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                            {{ $curso->type === 'Online' ? 'Online' : 'Presencial' }}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Vagas:</strong>
                                        {{ $curso->maximum_enrollments - $curso->registrations()->count() }}/{{ $curso->maximum_enrollments }}
                                    </p>
                                    <p>
                                        <strong>Data Limite:</strong>
                                        {{ $curso->registration_deadline->format('d/m/Y H:i') }}
                                    </p>
                                    <p>
                                        <strong>Inscritos:</strong>
                                        {{ $curso->registrations()->count() }}
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('cursos.show', $curso) }}"
                                        class="flex-1 text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Ver Detalhes
                                    </a>

                                    @if (auth()->user()->registrations()->where('cursos_id', $curso->id)->exists())
                                        <button disabled
                                            class="flex-1 bg-gray-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                                            Matriculado
                                        </button>
                                    @elseif($curso->registration_deadline->isPast())
                                        <button disabled
                                            class="flex-1 bg-red-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                                            Inscrição Encerrada
                                        </button>
                                    @elseif($curso->maximum_enrollments - $curso->registrations()->count() <= 0)
                                        <button disabled
                                            class="flex-1 bg-red-400 text-white font-bold py-2 px-4 rounded cursor-not-allowed">
                                            Sem Vagas
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('cursos.enroll', $curso) }}"
                                            class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                Matricular
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $cursos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
