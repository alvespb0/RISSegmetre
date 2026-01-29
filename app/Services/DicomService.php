<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Exception;

class DicomService
{
    public function downloadInstance($idEnc){
        $id = Crypt::decryptString($idEnc);

        $endPoint = env('ORTHANC_SERVER').'/instances'.'/'.$id.'/file';

        try{
            $response = Http::get($endPoint);

            if ($response->status() === 404) {
                return null;
            }
            
            if (!$response->ok()) {
                throw new \RuntimeException('Erro ao comunicar com Orthanc');
            }

            return $response->body(); 
        }catch(\Exception $e){
            \Log::error('Erro ao baixar imagem DCM: ' . $id . ', erro: '. $e->getMessage());
            throw $e;
        }

    }
}
?>