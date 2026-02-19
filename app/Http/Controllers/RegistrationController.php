<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Student;
use App\Models\Registration;
use App\Http\Requests\StoreRegistrationRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');
        $sort_by = request('sort_by', 'created_at');
        $sort_order = request('sort_order', 'desc');
        $per_page = request('per_page', 15);

        $query = Registration::with('student', 'curso');

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('curso', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $registrations = $query->orderBy($sort_by, $sort_order)
            ->paginate($per_page);

        return view('registrations.index', compact('registrations', 'search', 'sort_by', 'sort_order', 'per_page'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $students = Student::all();
        $cursos = Curso::all();

        return view('registrations.create', compact('students', 'cursos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Registration::create($validated);

        return redirect()->route('registrations.index')
            ->with('success', 'Matrícula criada com sucesso!');
    }

    /**
     * Display the authenticated user's registrations.
     */
    public function my(): View
    {
        $user = auth()->user();
        $registrations = $user->registrations()
            ->with('curso')
            ->latest()
            ->get();

        return view('registrations.my', compact('registrations'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registration $registration): RedirectResponse
    {
        $registration->delete();

        return redirect()->route('registrations.index')
            ->with('success', 'Matrícula cancelada com sucesso!');
    }

    /**
     * Cancel the specified registration (for students).
     */
    public function cancel(Registration $registration): RedirectResponse
    {
        $user = auth()->user();

        // Ensure the user can only cancel their own registrations
        if ($registration->user_id !== $user->id) {
            return redirect()->route('registrations.my')
                ->with('error', 'Você não tem permissão para cancelar esta matrícula.');
        }

        $registration->delete();

        return redirect()->route('registrations.my')
            ->with('success', 'Matrícula cancelada com sucesso!');
    }

    /**
     * Bulk delete registrations
     */
    public function bulkDelete(): RedirectResponse
    {
        $ids = request()->validate([
            'ids' => 'required|string',
        ])['ids'];

        $ids = array_map('intval', explode(',', $ids));

        Registration::whereIn('id', $ids)->delete();

        return redirect()->route('registrations.index')
            ->with('success', count($ids) . ' matrícula(s) canceladas com sucesso!');
    }
}
