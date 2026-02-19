<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory;

    protected $table = 'registrations';

    protected $fillable = [
        'students_id',
        'cursos_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'cursos_id');
    }
}
