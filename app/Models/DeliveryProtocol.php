<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProtocol extends Model
{
    protected $table = 'delivery_protocols';

    use HasFactory;

    protected $fillable = [
        'laudo_id',
        'protocolo',
        'senha', #hash
        'protocolo_path',
        'visualizado', #bool
        'first_view_at', #dateTime
        'last_view_at', #dateTime
    ];

    public function serie(){
        return $this->belongsTo(Serie::class, 'laudo_id');
    }
}
