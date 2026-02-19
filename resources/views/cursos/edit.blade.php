<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Curso') }} - {{ $curso->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('cursos.update', $curso) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome
                                do Curso</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $curso->name) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('description') border-red-500 @enderror">{{ old('description', $curso->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Curso</label>
                            <select name="type" id="type"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('type') border-red-500 @enderror"
                                required>
                                <option value="">Selecione um tipo</option>
                                <option value="Online" {{ old('type', $curso->type) === 'Online' ? 'selected' : '' }}>
                                    Online</option>
                                <option value="InPerson"
                                    {{ old('type', $curso->type) === 'InPerson' ? 'selected' : '' }}>Presencial</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="maximum_enrollments"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Máximo de
                                Inscrições</label>
                            <input type="number" name="maximum_enrollments" id="maximum_enrollments"
                                value="{{ old('maximum_enrollments', $curso->maximum_enrollments) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('maximum_enrollments') border-red-500 @enderror"
                                required min="1">
                            @error('maximum_enrollments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_deadline"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data Limite de
                                Inscrição</label>
                            <input type="datetime-local" name="registration_deadline" id="registration_deadline"
                                value="{{ old('registration_deadline', $curso->registration_deadline->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 @error('registration_deadline') border-red-500 @enderror"
                                required>
                            @error('registration_deadline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar Curso
                            </button>
                            <a href="{{ route('cursos.index') }}"
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
