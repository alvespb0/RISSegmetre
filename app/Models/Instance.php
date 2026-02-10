<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Instance extends Model
{
    protected $table = 'instances';

    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'serie_id',
        'instance_external_id',
        'file_uuid',
        'anamnese',
        'liberado_tec',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->useLogName('instances'); // Nome da "gaveta" no log
    }

    public function serie(){
        return $this->belongsTo(Serie::class, 'serie_id');
    }

}
