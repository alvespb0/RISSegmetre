<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\EmpresasSocService;

class EmpresasSoc extends Model
{
    protected $table = 'empresas_soc';

    use HasFactory;

    protected $fillable = [
        'nome',
        'codigo_soc',
        'cnpj',
    ];
}
