<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $table = 'instances';

    use HasFactory;

    protected $fillable = [
        'serie_id',
        'medico_id',
        'instance_external_id',
        'file_uuid',
        'anamnese',
        'status',
        'liberado_tec',
    ];

    public function serie(){
        return $this->belongsTo(Serie::class, 'serie_id');
    }

    public function medico(){
        return $this->belongsTo(User::class, 'medico_id');
    }
}
