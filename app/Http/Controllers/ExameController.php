<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class ExameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('exames.index');
    }

    public function getDicomFile($id){
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
}

