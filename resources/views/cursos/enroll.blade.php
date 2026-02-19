@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Matricular no Curso</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-xl font-semibold mb-4">{{ $curso->name }}</h2>
            <p class="mb-4">{{ $curso->description }}</p>

            <form method="POST" action="{{ route('cursos.enroll', $curso) }}">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Matricular-se
                </button>
            </form>
        </div>
    </div>
@endsection
