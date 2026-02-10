<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MedicoLaudo extends Model
{
    protected $table = 'medicos_laudo';

    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'empresas_laudo_id',
        'nome',
        'especialidade',
        'conselho_classe',
        'signature_path',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName('medico'); // Nome da "gaveta" no log
    }

    public function empresa(){
        return $this->belongsTo(EmpresaLaudo::class, 'empresas_laudo_id');
    }
    
    public function laudo(){
        return $this->hasMany(Laudo::class, 'medico_id');
    }
}
