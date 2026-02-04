<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Models\Patient;
use App\Models\Study;
use App\Models\Serie;
use App\Models\Instance;

use Carbon\Carbon;

/**
 * Serviço responsável pela integração com o servidor DICOM Orthanc.
 * Realiza o consumo da API REST do Orthanc para sincronizar Pacientes,
 * Estudos, Séries e Instâncias com o banco de dados local.
 */
class OrthancService
{
    /**
     * Obtém todos os estudos do Orthanc e sincroniza recursivamente com o banco local.
     *
     * @return bool Retorna true em caso de sucesso; false em caso de falha tratada.
     */
    public function getStudies(){
        $endPoint = env('ORTHANC_SERVER').'/studies';

        try{
            $response = Http::get($endPoint, [
                'expand' => ''
            ]);

            if(!$response->ok()){
                \Log::erro('Não encontrado nenhum estudo para o endpoint', ['endpoint' => $endPoint]);
                return false;
            }
            $data = $response->json();
            \Log::info(
                'Preparando para salvar DICOMs meta no banco',
                ['data' => $data]
            );

            foreach($data as $d){
                /* updateOrCreate de paciente */
                $patientName = str_replace('^', ' ', $d['PatientMainDicomTags']['PatientName']);
                $birthDate = isset($d['PatientMainDicomTags']['PatientBirthDate'])
                    ? Carbon::parse($d['PatientMainDicomTags']['PatientBirthDate'])->toDateString()
                    : null;

                $paciente = Patient::updateOrCreate(
                    ['patient_external_id' => $d['ParentPatient']],
                    [
                        'nome' => $patientName,
                        'birth_date' => $birthDate,
                        'patient_cpf' => $d['PatientMainDicomTags']['PatientID'],
                        'sexo' => $d['PatientMainDicomTags']['PatientSex'],
                    ]
                );
                
                /* updateOrCreate de estudo */
                $solicitante = str_replace('^', ' ', $d['MainDicomTags']['ReferringPhysicianName']);
                $solicitante = trim(preg_replace('/\s+/', ' ', $solicitante));
                
                $studyDate = isset($d['MainDicomTags']['StudyDate'])
                    ? Carbon::parse($d['MainDicomTags']['StudyDate'])->toDateString()
                    : null;
                $study = Study::updateOrCreate(
                    ['study_external_id' => $d['ID']],
                    [
                        'patient_id' => $paciente->id,
                        'study_instance_id' => $d['MainDicomTags']['StudyInstanceUID'],
                        'solicitante' => $solicitante,
                        'study_date' => $studyDate
                    ]
                );

                if(!empty($d['Series'])){
                    $series = $d['Series'];
                    foreach($series as $serie){
                        $this->getSeries($study->id, $serie);
                    }
                }
            }
            
            return true;
        }catch (\Exception $e) {
            \Log::error("Erro: " . $e->getMessage());
        }
    }

    /**
     * Obtém e salva os dados de uma Série específica.
     *
     * @param int $studyId ID interno do estudo no banco de dados.
     * @param string $serieId ID externo (UUID) da série no Orthanc.
     * @return bool Retorna true em caso de sucesso; false em caso de falha tratada.
     */
    public function getSeries($studyId, $serieId){
        $endPoint = env('ORTHANC_SERVER').'/series'.'/'.$serieId;

        try{
            $response = Http::get($endPoint, [
                'expand' => ''
            ]);

            if(!$response->ok()){
                \Log::error('Não localizado serie para endpoint informado.', ['endpoint' => $endPoint]);
                return false;
            }
           
            $data = $response->json();
            $serie = Serie::updateOrCreate(
                ['serie_external_id' => $serieId],
                [
                    'study_id' => $studyId,
                    'modality' => $data['MainDicomTags']['Modality'],
                    'body_part_examined' => $data['MainDicomTags']['BodyPartExamined']
                ]
            );
            
            foreach($data['Instances'] as $instance){
                $this->getInstance($serie->id, $instance);
            }
            return true;
        }catch (\Exception $e) {
            \Log::error("Erro: " . $e->getMessage());
        }
    }

    /**
     * Obtém e salva os dados de uma Instância (imagem/arquivo) específica.
     *
     * @param int $serieId ID interno da série no banco de dados.
     * @param string $instanceId ID externo (UUID) da instância no Orthanc.
     * @return bool Retorna true em caso de sucesso; false em caso de falha tratada.
     */
    public function getInstance($serieId, $instanceId){
        $endPoint = env('ORTHANC_SERVER').'/instances'.'/'.$instanceId;

        try{
            $response = Http::get($endPoint, [
                'expand' => ''
            ]);

            if(!$response->ok()){
                \Log::error('Não localizado instância para endpoint informado.', ['endpoint' => $endPoint]);
                return false;
            }
           
            $data = $response->json();
            Instance::updateOrCreate(
                ['instance_external_id' => $instanceId],
                [
                    'serie_id' => $serieId,
                    'file_uuid' => $data['FileUuid']
                ]
            );
            return true;
        }catch (\Exception $e) {
            \Log::error("Erro: " . $e->getMessage());
        }

    }
}
?>