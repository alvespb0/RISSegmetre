<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

use App\Models\Serie;
use App\Models\Study;
use Illuminate\Support\Facades\Storage;

class ExameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('exames.index');
    }

    public function getDicomFile($idEnc){
        $id = Crypt::decryptString($idEnc);

        $endPoint = env('ORTHANC_SERVER').'/instances'.'/'.$id.'/file';

        try{
            $response = Http::get($endPoint);

            if(!$response->ok()){
                \Log::error('Não localizado instância para endpoint informado.', ['endpoint' => $endPoint]);
                 abort(500);
            }

            return response($response->body(), 200, [
                'Content-Type' => 'application/dicom',
                'Content-Disposition' => 'attachment; filename="exame-' . $id . '.dcm"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }catch(\Exception $e){
            \Log::error('Erro ao baixar imagem DCM: ' . $id . ', erro: '. $e->getMessage());
            abort(500);
        }
    }

    public function getLaudoFile($idEnc){
        $id = Crypt::decryptString($idEnc);

        $serie = Serie::findOrFail($id);

        $path = $serie->laudo()->where('ativo', true)->first()?->laudo_path;

        if (!Storage::exists($path)) {
            \Log::error("Erro ao baixar laudo da serie: {$id}, path inexistente");
            abort(404);
        }

        return Storage::download($path);
    }

    public function getProtocoloFile($idEnc){
        $id = Crypt::decryptString($idEnc);

        $serie = Serie::findOrFail($id);

        $path = $serie->protocolo->protocolo_path;

        if(!file_exists($path)){
            \Log::error('Erro ao baixar Protocolo de entrega da serie: '.$id.', path inexistente');
            abort(500);
        }

        return response()->download($path);
    }
}

