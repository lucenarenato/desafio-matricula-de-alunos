<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Http\Requests\StoreCursoRequest;
use App\Http\Requests\UpdateCursoRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');
        $type = request('type');
        $sort_by = request('sort_by', 'created_at');
        $sort_order = request('sort_order', 'desc');
        $per_page = request('per_page', 15);

        $query = Curso::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $cursos = $query->orderBy($sort_by, $sort_order)
            ->paginate($per_page);

        return view('cursos.index', compact('cursos', 'search', 'type', 'sort_by', 'sort_order'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('cursos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCursoRequest $request): RedirectResponse
    {
        Curso::create($request->validated());

        return redirect()->route('cursos.index')
            ->with('success', 'Curso criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Curso $curso): View
    {
        $curso->load('registrations.student');
        return view('cursos.show', compact('curso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curso $curso): View
    {
        return view('cursos.edit', compact('curso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCursoRequest $request, Curso $curso): RedirectResponse
    {
        $curso->update($request->validated());

        return redirect()->route('cursos.index')
            ->with('success', 'Curso atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curso $curso): RedirectResponse
    {
        $curso->delete();

        return redirect()->route('cursos.index')
            ->with('success', 'Curso deletado com sucesso!');
    }

    /**
     * Bulk delete cursos
     */
    public function bulkDelete(): RedirectResponse
    {
        $ids = request()->validate([
            'ids' => 'required|string',
        ])['ids'];

        $ids = array_map('intval', explode(',', $ids));

        Curso::whereIn('id', $ids)->delete();

        return redirect()->route('cursos.index')
            ->with('success', count($ids) . ' curso(s) deletado(s) com sucesso!');
    }

    /**
     * Display a listing of courses available for enrollment (for students).
     */
    public function list(): View
    {
        $search = request('search');
        $type = request('type');
        $sort_by = request('sort_by', 'created_at');
        $sort_order = request('sort_order', 'desc');
        $per_page = request('per_page', 12);

        $query = Curso::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        $cursos = $query->orderBy($sort_by, $sort_order)
            ->paginate($per_page);

        return view('cursos.list', compact('cursos', 'search', 'type', 'sort_by', 'sort_order'));
    }

    /**
     * Enroll the authenticated user in a course.
     */
    public function enroll(Curso $curso): RedirectResponse
    {
        $user = auth()->user();

        if ($user->registrations()->where('cursos_id', $curso->id)->exists()) {
            return redirect()->route('cursos.list')->with('error', 'Você já está matriculado neste curso.');
        }

        if ($curso->maximum_enrollments - $curso->registrations()->count() <= 0) {
            return redirect()->route('cursos.list')->with('error', 'Este curso não tem mais vagas disponíveis.');
        }

        if ($curso->registration_deadline->isPast()) {
            return redirect()->route('cursos.list')->with('error', 'O prazo de inscrição para este curso já encerrou.');
        }

        $user->registrations()->create(['cursos_id' => $curso->id]);

        return redirect()->route('cursos.list')->with('success', 'Matrícula realizada com sucesso!');
    }
}
