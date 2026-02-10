<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Serie;
use Illuminate\Support\Facades\Crypt;

class PatientExam extends Component
{
    public $serie;
    public $protocolo;

    public function mount(Serie $serie){
        $this->serie = $serie;
        $this->protocolo = session()->get('patient_protocol');
    }

    public function downloadLaudo(){
        $protocoloEnc = Crypt::encryptString($this->protocolo);

        activity('downloads')
                ->performedOn($this->serie)
                ->withProperties([
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                    'plataforma' => request()->header('sec-ch-ua-platform'),
                    'protocolo' => $this->protocolo
                ])
                ->log('Fez o download do laudo pelo protocolo de entrega.');

        return redirect()->route('patient.download.laudo', $protocoloEnc);
    }

    public function downloadImagemJpg($instance_external_id){
        $idEnc = Crypt::encryptString($instance_external_id);

        activity('downloads')
                ->performedOn($this->serie)
                ->withProperties([
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                    'plataforma' => request()->header('sec-ch-ua-platform'),
                    'protocolo' => $this->protocolo
                ])
                ->log('Fez o download das imagens JPG pelo protocolo de entrega.');

        return redirect()->route('patient.download.imagem', $idEnc);
    }
    public function render()
    {
        return view('livewire.patient-exam');
    }
}
