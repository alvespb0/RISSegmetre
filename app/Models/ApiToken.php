<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $table = 'api_tokens';

    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'active',
    ];

    protected $hidden = ['token'];

    public function empresa(){
        return $this->hasOne(EmpresaLaudo::class, 'token_id');
    }
}
