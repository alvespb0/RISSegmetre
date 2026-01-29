<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Study extends Model
{
    protected $table = 'studies';

    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'patient_id',
        'study_external_id',
        'study_instance_id',
        'solicitante',
        'study_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName('studies'); // Nome da "gaveta" no log
    }
    
    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function serie(){
        return $this->hasMany(Serie::class, 'study_id');
    }
}
