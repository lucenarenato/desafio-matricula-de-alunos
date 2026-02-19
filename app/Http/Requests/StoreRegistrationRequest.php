<?php

namespace App\Http\Requests;

use App\Models\Curso;
use App\Models\Registration;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'students_id' => 'required|exists:students,id',
            'cursos_id' => 'required|exists:cursos,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'students_id.required' => 'O aluno é obrigatório.',
            'students_id.exists' => 'O aluno selecionado é inválido.',
            'cursos_id.required' => 'O curso é obrigatório.',
            'cursos_id.exists' => 'O curso selecionado é inválido.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $studentsId = $this->students_id;
            $cursosId = $this->cursos_id;

            if (!$studentsId || !$cursosId) {
                return;
            }

            // Check for duplicate enrollment
            $exists = Registration::where('students_id', $studentsId)
                ->where('cursos_id', $cursosId)
                ->exists();

            if ($exists) {
                $validator->errors()->add('cursos_id', 'Este aluno já está inscrito neste curso!');
            }

            // Check course availability
            $curso = Curso::find($cursosId);
            if ($curso) {
                if ($curso->getEnrolledCountAttribute() >= $curso->maximum_enrollments) {
                    $validator->errors()->add('cursos_id', 'Este curso não possui mais vagas disponíveis!');
                }

                // Check registration deadline
                if (!$curso->isRegistrationOpenAttribute()) {
                    $validator->errors()->add('cursos_id', 'O período de inscrição para este curso foi encerrado!');
                }
            }
        });
    }
}
