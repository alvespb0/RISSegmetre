<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $table = 'studies';

    use HasFactory;

    protected $fillable = [
        'patient_id',
        'study_external_id',
        'study_instance_id',
        'anamnese',
        'solicitante',
        'study_date',
        'liberado_tec'
    ];

    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function serie(){
        return $this->hasMany(Serie::class, 'study_id');
    }
}
