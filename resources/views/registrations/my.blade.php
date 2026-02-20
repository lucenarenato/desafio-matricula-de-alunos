<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meus Cursos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($registrations->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <p class="mb-4">{{ __('Você ainda não está matriculado em nenhum curso.') }}</p>
                        <a href="{{ route('cursos.list') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Matricular-se em um Curso
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($registrations as $registration)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $registration->curso->name }}
                                </h3>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    {{ Str::limit($registration->curso->description, 100) }}
                                </p>

                                <div class="space-y-2 mb-4 text-sm text-gray-700 dark:text-gray-300">
                                    <p>
                                        <strong>Tipo:</strong>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $registration->curso->type === 'Online' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                            {{ $registration->curso->type === 'Online' ? 'Online' : 'Presencial' }}
                                        </span>
                                    </p>
                                    <p>
                                        <strong>Data de Inscrição:</strong>
                                        {{ $registration->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <p>
                                        <strong>Status:</strong>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Matriculado
                                        </span>
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('cursos.show', $registration->curso) }}"
                                        class="flex-1 text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Ver Detalhes
                                    </a>
                                    <button type="button"
                                        class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="confirmCancelRegistration('{{ route('registrations.cancel', $registration) }}')">
                                        Cancelar Inscrição
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-center">
                    <a href="{{ route('cursos.list') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Ver Mais Cursos
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmCancelRegistration(url) {
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Deseja realmente cancelar sua inscrição neste curso?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Não'
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
    </script>
</x-app-layout>
