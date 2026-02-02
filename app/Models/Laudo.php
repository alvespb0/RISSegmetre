<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laudo extends Model
{
    protected $table = 'laudos';

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'study_id',
        'empresa_id',
        'medico_id',
        'laudo', #texto
        'laudo_path',
        'laudo_assinado',
        'ativo'
    ];

    public function study(){
        return $this->belongsTo(Study::class, 'study_id');
    }

    public function empresa(){
        return $this->belongsTo(EmpresaLaudo::class, 'empresa_id');
    }

    public function medico(){
        return $this->belongsTo(MedicoLaudo::class, 'medico_id');
    }
}
