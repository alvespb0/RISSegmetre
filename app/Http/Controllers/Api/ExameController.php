<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DicomService;

use App\Http\Requests\ExamesIndexRequest;
use App\Http\Requests\LaudoRequest;
use Illuminate\Http\Request;

use App\Models\Study;
use App\Models\Serie;
use App\Models\Instance;
use App\Models\Laudo;
use App\Models\MedicoLaudo;
use App\Models\ApiToken;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

use Exception;

class ExameController extends Controller
{
    /**
     * Lista os exames (Studies) com filtros, paginação e mapeamento de dados.
     * * Este método filtra exames por data e status, garante que apenas instâncias
     * liberadas tecnicamente sejam exibidas e retorna uma resposta paginada.
     *
     * @param  ExamesIndexRequest  $request Objeto de validação contendo filtros (study_date, status, per_page).
     * @return \Illuminate\Http\JsonResponse Resposta JSON contendo a coleção de exames e metadados de paginação.
     */
    public function index(ExamesIndexRequest $request)
    {
        $perPage = min(
            (int) $request->query('per_page', 10),
            10
        );

        $exames = Study::query();

        if(!empty($request->study_date)){
            $exames->whereDate('study_date', $request->study_date);
        }

        if(!empty($request->status)){
            $exames->where('status', $request->status);
        }

        $exames = $exames->with(['patient', 'serie.instance'])
                    ->whereHas('serie.instance', function ($q){
                        $q->where('liberado_tec', true);
                    })
                    ->orderByDesc('study_date')
                    ->paginate($perPage);
        
        return response()->json([
            'data' => $exames->getCollection()->map(
                fn($study) => $this->mapIndex($study)
            ),

            'meta' => [
                'current_page' => $exames->currentPage(),
                'per_page'     => $exames->perPage(),
                'total'        => $exames->total(),
                'last_page'    => $exames->lastPage(),
                'has_more'     => $exames->hasMorePages(),
            ]
        ], 200);
    }

    /**
     * Mapeia os dados de um Estudo (Study) para o formato de array de saída da API.
     *
     * @param  \App\Models\Study  $study Instância do modelo Study.
     * @return array<string, mixed> Estrutura formatada do estudo e paciente.
     */
    private function mapIndex(Study $study): array{
        return [
            'study_id' => $study->id,
            'study_date' => $study->study_date,
            'medico_solicitante' => $study->solicitante,
            'status' => $study->status,

            'patient' => [
                'id' => $study->patient->id,
                'nome' => $study->patient->nome,
                'data_nascimento' => $study->patient->birth_date,
                'sexo' => $study->patient->sexo 
            ],

            'series' => $this->mapSeries($study),
        ];
    }

    /**
     * Transforma as séries associadas a um estudo em um array formatado.
     *
     * @param  \App\Models\Study  $study Instância do modelo Study.
     * @return array<int, array> Lista de séries formatadas.
     */
    private function mapSeries(Study $study): array{
        $series = [];

        foreach($study->serie as $serie){
            $series[] = [
                'id' => $serie->id,
                'uuid' => Crypt::encryptString($serie->serie_external_id),
                'modality' => $serie->modality,
                'body_part_examined' => $serie->body_part_examined,
                'instances' => $this->mapInstances($serie)
            ];
        }

        return $series;
    }

    /**
     * Transforma as instâncias associadas a uma série em um array formatado.
     *
     * @param  \App\Models\Serie  $serie Instância do modelo Serie.
     * @return array<int, array> Lista de instâncias (exames técnicos) formatadas.
     */
    private function mapInstances(Serie $serie): array{
        $instances = [];

        foreach($serie->instance as $instance){
            $instances[] = [
                'uuid' => Crypt::encryptString($instance->instance_external_id),
                'anamnese' => $instance->anamnese,
                'liberado_tecnico' => $instance->liberado_tec
            ];
        }

        return $instances;
    }

    /**
     * Realiza o download de uma instância específica de arquivo DICOM.
     * * O método solicita o arquivo binário via DicomService, gera um nome aleatório
     * e define os headers apropriados para que o navegador trate o retorno 
     * como um download de arquivo médico (.dcm).
     *
     * @param  \App\Services\DicomService  $dicom         Serviço responsável pela lógica de recuperação do arquivo.
     * @param  string                      $instance_uuid Identificador único universal da instância DICOM.
     * * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse 
     * Retorna o arquivo binário em caso de sucesso (200), 
     * 404 se não encontrado ou 500 em caso de erro sistêmico.
     * * @throws \Throwable Captura e loga qualquer falha inesperada durante o processo de recuperação ou stream.
     */
    public function downloadDicom(DicomService $dicom, string $instance_uuid){
        try{
            $file = $dicom->downloadInstance($instance_uuid);

            if($file === null){
                return response()->json([
                    'error' => 'Instância não encontrada'
                ], 404);
            }

            $fileName = Str::random(12);

            return response($file, 200, [
                'Content-Type' => 'application/dicom',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.dcm"',
            ]);
        }catch (\Throwable $e) {
            \Log::error($e);
            return response()->json([
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exame = Study::find($id);
        
        if(!$exame){
            return response()->json([
                'estudo não encontrado com o id: '. $id
            ], 404);
        }

        return response()->json([
            'data' => $this->mapIndex($exame)
        ], 200);

    }

    /**
     * Salva ou atualiza o laudo radiológico de um estudo específico.
     *
     * Este método processa um PDF em Base64, valida a integridade do arquivo, 
     * gerencia o armazenamento físico no storage (removendo arquivos antigos se necessário) 
     * e atualiza o status do estudo e os dados da primeira série relacionada.
     *
     * @param  \App\Http\Requests\LaudoRequest  $request Objeto de requisição contendo:
     * - status: string (pendente, laudado, rejeitado)
     * - laudo_pdf: string (base64)
     * - laudo_texto: string
     * @param  int|string  $id ID único do estudo (Study).
     * * @return \Illuminate\Http\JsonResponse Respostas possíveis:
     * - 200: Sucesso ao salvar.
     * - 404: Estudo não encontrado.
     * - 422: Erro de validação ou PDF inválido.
     * - 500: Erro interno de processamento ou storage.
     * * @throws \Throwable Captura e loga falhas durante o upload ou atualização do banco.
     */
    public function setLaudo(LaudoRequest $request, $id){
        $serie = Serie::find($id);

        if(!$serie){
            return response()->json([
                'error' => 'Estudo não encontrado',
                'id' => $id
            ], 404);
        }

        $serie->first();

        $pdfBin = base64_decode($request->laudo_pdf, true);

        if ($pdfBin === false) {
            return response()->json([
                'error' => 'Base64 inválido'
            ], 422);
        }

        if (substr($pdfBin, 0, 4) !== '%PDF') {
            return response()->json([
                'error' => 'Arquivo não é um PDF válido'
            ], 422);
        }

        $fileName = 'laudo_'.Str::uuid().'.pdf';
        $filePath = 'laudos/'.$fileName;

        try{
            if(!empty($serie->laudo)){
                $serie->laudo()->update([
                    'ativo' => false
                ]);
            }
            storage::put($filePath, $pdfBin);

            $validaMed = $this->validaMedico($request->medico_id, $request->header('Authorization'));

            if($validaMed !== true){
                return response()->json([
                    'error' => $validaMed['error']
                ], 422);
            }

            $medico = MedicoLaudo::findOrFail($request->medico_id);

            $serie->study->update([
                'status' => $request->status
            ]);

            Laudo::create([
                'serie_id' => $serie->id,
                'medico_id' => $request->medico_id,
                'empresa_id' => $medico->empresa->id ?? null,
                'laudo' => $request->laudo_texto,
                'laudo_path' => $filePath,
            ]);

            dispatch(new \App\Jobs\SocJob());

            return response()->json([
                'message' => 'Laudo salvo com sucesso'
            ], 200);
        }catch (\Throwable $e) {
            \Log::error('Erro ao salvar laudo: '. $e);

            return response()->json([
                'error' => 'Erro interno ao salvar laudo'
            ], 500);
        }
    }

    private function validaMedico($medico_id, $authHeader){
        $token = substr($authHeader, 7);
        $hashedToken = hash('sha256', $token);

        $tokenModel = ApiToken::with('empresa.medico')
            ->where('token', $hashedToken)
            ->first();

        $empresa = $tokenModel->empresa;

        if (!$empresa) {
            return [
                'status' => false,
                'error' => 'Token não possui empresa vinculada'
            ];
        }

        if ($empresa->medico->isEmpty()) {
            return [
                'status' => false,
                'error' => 'Empresa não possui médico cadastrado.'
            ];
        }

        // Médico informado precisa pertencer à empresa
        if (!$empresa->medico->contains('id', $medico_id)) {
            return [
                'status' => false,
                'error' => 'Médico informado não pertence à empresa vinculada ao token'
            ];
        }

        return true;
    }

    public function setRejeicao(Request $request){

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
