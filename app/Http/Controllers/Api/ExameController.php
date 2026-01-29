<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExamesIndexRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Study;
use App\Models\Serie;
use App\Models\Instance;

use Carbon\Carbon;

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
            $status = $request->status;
            $exames->whereHas('serie.instance', function ($q) use ($status){
                $q->where('status', $status);
            });
        }

        $exames = $exames
                ->with(['patient', 'serie.instance'])
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
                'modality' => $serie->modality,
                'body_part_examined' => $serie->body_part_examined,
                'laudo_assinado' => $serie->laudo_assinado,
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
                'id' => $instance->id,
                'anamnese' => $instance->anamnese,
                'status' => $instance->status,
                'liberado_tecnico' => $instance->liberado_tec
            ];
        }

        return $instances;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
