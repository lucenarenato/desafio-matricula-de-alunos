<?php

namespace App\Http\Requests;

use App\Enums\CursoTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCursoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:cursos,name,' . $this->curso->id,
            'description' => 'nullable|string',
            'type' => ['required', new Enum(CursoTypes::class)],
            'maximum_enrollments' => 'required|integer|min:1',
            'registration_deadline' => 'required|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do curso é obrigatório.',
            'name.unique' => 'Este nome de curso já existe.',
            'type.required' => 'O tipo de curso é obrigatório.',
            'maximum_enrollments.required' => 'A quantidade máxima de matrículas é obrigatória.',
            'maximum_enrollments.min' => 'A quantidade máxima deve ser pelo menos 1.',
            'registration_deadline.required' => 'A data limite de inscrição é obrigatória.',
            'registration_deadline.after' => 'A data limite deve ser no futuro.',
        ];
    }
}
