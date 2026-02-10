<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\UploadLaudoSocService;

use App\Models\Study;
use App\Models\Integracao;

class SocController extends Controller
{
    public function requestUpload($studyId): array{
        $study = Study::findOrFail($studyId);
        $integracao = Integracao::where('slug', 'ws_soc_upload_ged')->first();

        if(!$study->laudo()){
            return [
                'status' => false,
                'message' => 'Nenhum laudo vinculado a esse estudo'
            ];
        }

        if(!$study->laudo()->where('ativo', true)->first()?->laudo_path){
            return [
                'status' => false,
                'message' => 'Laudo não encontrado no servidor'
            ];
        }

        $path = $study->laudo()->where('ativo', true)->first()?->laudo_path;
        $username = $integracao->username;
        $senha = $integracao->getDecryptedPassword();
        $codSequencial = $study->cod_sequencial_ficha;

        $service = new UploadLaudoSocService($username, $senha, $codSequencial, $path);

        $result = $service->requestUpload();

        if($result === false){
            return [
                'status' => false,
                'message' => 'Falha ao fazer upload do laudo no SOC'
            ];
        }

        return [
            'status' => true,
            'message' => 'Feito Upload do Laudo com Sucesso',
        ];
    }
}
