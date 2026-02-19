<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'name',
        'email',
        'date_of_birth',
        'phone',
        'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'students_id');
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'registrations', 'students_id', 'cursos_id');
    }
}
