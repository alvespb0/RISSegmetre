<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Serie extends Model
{
    protected $table = 'series';

    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'study_id',
        'serie_external_id',
        'medico_id',
        'modality', #enum
        'laudo',
        'laudo_path',
        'laudo_assinado',
        'motivo_rejeicao',
        'body_part_examined'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName('series'); // Nome da "gaveta" no log
    }

    public function study(){
        return $this->belongsTo(Study::class, 'study_id');
    } 

    public function instance(){
        return $this->hasMany(Instance::class, 'serie_id');
    }

    public function medico(){
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function protocolo(){
        return $this->hasOne(DeliveryProtocol::class, 'laudo_id');
    }
}
