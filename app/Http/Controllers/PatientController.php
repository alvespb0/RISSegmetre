<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryProtocol;

class PatientController extends Controller
{
    public function exames(){
        $protocolo = session()->get('patient_protocol');
        
        $modelProtocol = DeliveryProtocol::where('protocolo', $protocolo)->first();

        $serie = $modelProtocol->laudo;

        return view('exames/patient_index', ['serie' => $serie]);
    }
}
