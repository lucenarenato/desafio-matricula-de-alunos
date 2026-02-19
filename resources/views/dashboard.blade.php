<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Boas-vindas -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Bem-vindo, {{ auth()->user()->name }}!</h3>
                    <p>{{ __('Você está logado no sistema.') }}</p>
                </div>
            </div>

            @if (auth()->user()->isUser())
                <!-- Resumo para Alunos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Meus Cursos') }}
                            </h4>
                            <p class="text-3xl font-bold text-blue-600">
                                {{ auth()->user()->registrations()->count() }}
                            </p>
                            <a href="{{ route('registrations.my') }}"
                                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ver Meus Cursos
                            </a>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Cursos Disponíveis') }}
                            </h4>
                            <p class="text-3xl font-bold text-green-600">
                                {{ \App\Models\Curso::count() }}
                            </p>
                            <a href="{{ route('cursos.list') }}"
                                class="mt-4 inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Explorar Cursos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Últimos Cursos Matriculados -->
                @if (auth()->user()->registrations()->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Meus Últimos Cursos') }}
                            </h4>
                            <div class="space-y-3">
                                @foreach (auth()->user()->registrations()->with('curso')->latest()->limit(3)->get() as $registration)
                                    <div
                                        class="flex justify-between items-center p-3 border border-gray-200 dark:border-gray-700 rounded">
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $registration->curso->name }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Inscrição em: {{ $registration->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('cursos.show', $registration->curso) }}"
                                            class="text-blue-600 hover:text-blue-900">Ver Detalhes</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @elseif(auth()->user()->isAdmin())
                <!-- Resumo para Administrador -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Total de Cursos') }}
                            </h4>
                            <p class="text-3xl font-bold text-blue-600">
                                {{ \App\Models\Curso::count() }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Total de Estudantes') }}
                            </h4>
                            <p class="text-3xl font-bold text-green-600">
                                {{ \App\Models\Student::count() }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ __('Total de Matrículas') }}
                            </h4>
                            <p class="text-3xl font-bold text-purple-600">
                                {{ \App\Models\Registration::count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
