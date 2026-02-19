<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Matrícula') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('registrations.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="students_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aluno</label>
                            <select name="students_id" id="students_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('students_id') border-red-500 @enderror"
                                required>
                                <option value="">Selecione um aluno</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('students_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('students_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cursos_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Curso</label>
                            <select name="cursos_id" id="cursos_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('cursos_id') border-red-500 @enderror"
                                required>
                                <option value="">Selecione um curso</option>
                                @foreach ($cursos as $curso)
                                    <option value="{{ $curso->id }}"
                                        {{ old('cursos_id') == $curso->id ? 'selected' : '' }}>
                                        {{ $curso->name }}
                                        ({{ $curso->registrations()->count() }}/{{ $curso->maximum_enrollments }}
                                        - {{ $curso->type === 'Online' ? 'Online' : 'Presencial' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cursos_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Criar Matrícula
                            </button>
                            <a href="{{ route('registrations.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
