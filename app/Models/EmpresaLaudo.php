<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpresaLaudo extends Model
{
    protected $table = 'empresas_laudo';

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'token_id', # 1:1
        'nome',
        'cnpj',
    ];

    public function token(){
        return $this->belongsTo(ApiToken::class, 'token_id');
    }

    public function medico(){
        return $this->hasMany(MedicoLaudo::class, 'empresas_laudo_id');
    }

    public function laudo(){
        return $this->hasMany(Laudo::class, 'empresa_id');
    }
}
