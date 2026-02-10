<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Laudo extends Model
{
    protected $table = 'laudos';

    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'serie_id',
        'empresa_id',
        'medico_id',
        'laudo', #texto
        'laudo_path',
        'laudo_assinado',
        'ativo'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName('laudo'); // Nome da "gaveta" no log
    }

    public function serie(){
        return $this->belongsTo(Serie::class, 'serie_id');
    }

    public function empresa(){
        return $this->belongsTo(EmpresaLaudo::class, 'empresa_id');
    }

    public function medico(){
        return $this->belongsTo(MedicoLaudo::class, 'medico_id');
    }
}
