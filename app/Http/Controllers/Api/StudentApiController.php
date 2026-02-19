<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentApiController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Student::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        }

        $students = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Alunos listados com sucesso',
            'data' => $students->items(),
            'pagination' => [
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'from' => $students->firstItem(),
                'to' => $students->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students',
                'date_of_birth' => 'required|date|before:today',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            $student = Student::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Aluno criado com sucesso',
                'data' => $student,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar aluno',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified student
     */
    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $student->load('registrations', 'cursos'),
        ]);
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email,' . $student->id,
                'date_of_birth' => 'required|date|before:today',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            $student->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Aluno atualizado com sucesso',
                'data' => $student,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar aluno',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete the specified student
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Aluno deletado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar aluno',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete students
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:students,id',
            ])['ids'];

            Student::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' aluno(s) deletado(s) com sucesso',
                'deleted_count' => count($ids),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar alunos',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }
}
