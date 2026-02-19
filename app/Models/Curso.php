<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    /** @use HasFactory<\Database\Factories\CursoFactory> */
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'name',
        'description',
        'type',
        'maximum_enrollments',
        'registration_deadline',
    ];

    protected $casts = [
        'registration_deadline' => 'datetime',
        'type' => 'string',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'cursos_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'registrations', 'cursos_id', 'students_id');
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    public function getAvailableSpotsAttribute(): int
    {
        return $this->maximum_enrollments - $this->getEnrolledCountAttribute();
    }

    public function isFullAttribute(): bool
    {
        return $this->getAvailableSpotsAttribute() <= 0;
    }

    public function isRegistrationOpenAttribute(): bool
    {
        return now()->isBefore($this->registration_deadline);
    }
}
