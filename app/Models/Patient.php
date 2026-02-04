<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    use HasFactory;

    protected $fillable = [
        'nome',
        'patient_external_id',
        'birth_date',
        'patient_cpf',
        'sexo' # enum
    ];

    public function study(){
        return $this->hasMany(Study::class, 'patient_id');
    }
}
