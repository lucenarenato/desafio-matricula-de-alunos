<?php

namespace App\Http\Controllers\Api;

use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CursoApiController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of cursos
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $type = $request->input('type');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Curso::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($type) {
            $query->where('type', $type);
        }

        $cursos = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Cursos listados com sucesso',
            'data' => $cursos->items(),
            'pagination' => [
                'total' => $cursos->total(),
                'per_page' => $cursos->perPage(),
                'current_page' => $cursos->currentPage(),
                'last_page' => $cursos->lastPage(),
                'from' => $cursos->firstItem(),
                'to' => $cursos->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created curso
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:cursos|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:On-line,Presencial',
                'maximum_enrollments' => 'required|integer|min:1',
                'registration_deadline' => 'required|date|after:today',
            ]);

            $curso = Curso::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Curso criado com sucesso',
                'data' => $curso,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar curso',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified curso
     */
    public function show(Curso $curso): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $curso->load('registrations', 'students'),
        ]);
    }

    /**
     * Update the specified curso
     */
    public function update(Request $request, Curso $curso): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:cursos,name,' . $curso->id . '|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:On-line,Presencial',
                'maximum_enrollments' => 'required|integer|min:1',
                'registration_deadline' => 'required|date|after:today',
            ]);

            $curso->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Curso atualizado com sucesso',
                'data' => $curso,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar curso',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete the specified curso
     */
    public function destroy(Curso $curso): JsonResponse
    {
        try {
            $curso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Curso deletado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar curso',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete cursos
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:cursos,id',
            ])['ids'];

            Curso::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' curso(s) deletado(s) com sucesso',
                'deleted_count' => count($ids),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar cursos',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }
}
