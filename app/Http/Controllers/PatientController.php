<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryProtocol;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function exames(){
        $protocolo = session()->get('patient_protocol');
        
        $modelProtocol = DeliveryProtocol::where('protocolo', $protocolo)->firstOrFail();

        $serie = $modelProtocol->serie;

        return view('exames/patient_index', ['serie' => $serie]);
    }

    public function downloadLaudo($protocoloEnc){
        $protocolo = Crypt::decryptString($protocoloEnc);

        $sessionProtocol = session()->get('patient_protocol');
        
        if ($protocolo !== $sessionProtocol) {
            abort(403, 'Acesso não autorizado.');
        }

        $modelProtocol = DeliveryProtocol::where('protocolo', $protocolo)->firstOrFail();

        $path = $modelProtocol->serie->study
                            ->laudo()
                            ->where('ativo', true)
                            ->first()
                            ->laudo_path;

        if (!Storage::exists($path)) {
            \Log::error(
                'Erro ao baixar laudo do protocolo: '.$protocoloEnc.', path inexistente: '.$path
            );
            abort(404);
        }

        return Storage::download($path);
    }

    public function downloadImagemJpg($idEnc){ #external_id
        $id = Crypt::decryptString($idEnc);

        $sessionProtocol = session()->get('patient_protocol');

        $modelProtocol = DeliveryProtocol::where('protocolo', $sessionProtocol)->firstOrFail();

        if(! $modelProtocol->serie->instance()->where('instance_external_id', $id)->exists()){
            abort(403, 'Acesso não autorizado.');
        }

        $endPoint = env('ORTHANC_SERVER').'/instances'.'/'.$id.'/rendered';

        try{
            $response = Http::get($endPoint,[
                'use-dicom-windowing' => 'true',
                'quality' => 95,
                'smooth' => 1,
            ]);

            if(!$response->ok()){
                \Log::error('Não localizado instância para endpoint informado.', ['endpoint' => $endPoint]);
                 abort(500);
            }

            return response($response->body(), 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'attachment; filename="exame-' . $id . '.jpg"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }catch(\Exception $e){
            \Log::error('Erro ao baixar imagem jpg, erro: '. $e->getMessage());
            abort(500);
        }
    }

}
