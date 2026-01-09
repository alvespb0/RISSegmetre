<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $table = 'series';

    use HasFactory;

    protected $fillable = [
        'study_id',
        'serie_external_id',
        'modality', #enum
        'body_part_examined'
    ];

    public function study(){
        return $this->belongsTo(Study::class, 'study_id');
    } 

    public function instance(){
        return $this->hasMany(Instance::class, 'serie_id');
    }
}
