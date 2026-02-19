<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Curso') }} - {{ $curso->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">{{ $curso->name }}</h3>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tipo</p>
                            <p class="text-lg">
                                <span
                                    class="px-3 py-1 rounded-full {{ $curso->type === 'Online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $curso->type === 'Online' ? 'Online' : 'Presencial' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Inscritos</p>
                            <p class="text-lg">{{ $curso->registrations()->count() }}/{{ $curso->maximum_enrollments }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data Limite</p>
                            <p class="text-lg">{{ $curso->registration_deadline->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                            <p class="text-lg">
                                @if ($curso->isFullAttribute())
                                    <span class="text-red-600 font-bold">LOTADO</span>
                                @elseif(!$curso->isRegistrationOpenAttribute())
                                    <span class="text-red-600 font-bold">FECHADO</span>
                                @else
                                    <span class="text-green-600 font-bold">ABERTO</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($curso->description)
                        <div className="mb-6">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Descrição</p>
                            <p class="text-lg">{{ $curso->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Alunos inscritos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Alunos Inscritos</h4>
                </div>
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
                                    Data Inscrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($curso->registrations as $registration)
                                <tr
                                    class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->student->name }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->student->email }}</td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                                        {{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-900 dark:text-gray-100">
                                        Nenhum aluno inscrito</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 text-right space-x-2">
                    <a href="{{ route('cursos.edit', $curso) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Editar
                    </a>
                    <a href="{{ route('cursos.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
