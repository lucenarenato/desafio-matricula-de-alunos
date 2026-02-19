<?php

namespace App\Http\Controllers\Api;

use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistrationApiController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of registrations
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Registration::with('student', 'curso');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('curso', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Matrículas listadas com sucesso',
            'data' => $registrations->items(),
            'pagination' => [
                'total' => $registrations->total(),
                'per_page' => $registrations->perPage(),
                'current_page' => $registrations->currentPage(),
                'last_page' => $registrations->lastPage(),
                'from' => $registrations->firstItem(),
                'to' => $registrations->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created registration
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'students_id' => 'required|exists:students,id',
                'cursos_id' => 'required|exists:cursos,id',
            ]);

            // Check for duplicate enrollment
            $exists = Registration::where('students_id', $validated['students_id'])
                ->where('cursos_id', $validated['cursos_id'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este aluno já está inscrito neste curso!',
                ], 409);
            }

            // Check course availability
            $curso = Curso::find($validated['cursos_id']);
            if ($curso->getEnrolledCountAttribute() >= $curso->maximum_enrollments) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este curso não possui mais vagas disponíveis!',
                ], 409);
            }

            // Check registration deadline
            if (!$curso->isRegistrationOpenAttribute()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O período de inscrição para este curso foi encerrado!',
                ], 409);
            }

            $registration = Registration::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Matrícula criada com sucesso',
                'data' => $registration->load('student', 'curso'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar matrícula',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified registration
     */
    public function show(Registration $registration): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $registration->load('student', 'curso'),
        ]);
    }

    /**
     * Delete the specified registration
     */
    public function destroy(Registration $registration): JsonResponse
    {
        try {
            $registration->delete();

            return response()->json([
                'success' => true,
                'message' => 'Matrícula deletada com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar matrícula',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete registrations
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:registrations,id',
            ])['ids'];

            Registration::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' matrícula(s) deletada(s) com sucesso',
                'deleted_count' => count($ids),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar matrículas',
                'errors' => $e->getMessage(),
            ], 422);
        }
    }
}
