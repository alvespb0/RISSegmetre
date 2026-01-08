<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Models\Patient;
use App\Models\Study;
use App\Models\Serie;
use App\Models\Instance;

use Carbon\Carbon;

class OrthancService
{
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

    public function getSeries($studyId, $serieId){
        $endPoint = env('ORTHANC_SERVER').'/series'.'/'.$serieId;

        try{
            $response = Http::get($endPoint, [
                'expand' => ''
            ]);

            if(!$response->ok()){
                \Log::error('Não localizado serie para endpoint informado.', ['endpoint' => $endpoint]);
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

    public function getInstance($serieId, $instanceId){
        $endPoint = env('ORTHANC_SERVER').'/instances'.'/'.$instanceId;

        try{
            $response = Http::get($endPoint, [
                'expand' => ''
            ]);

            if(!$response->ok()){
                \Log::error('Não localizado instância para endpoint informado.', ['endpoint' => $endpoint]);
                return false;
            }
           
            $data = $response->json();
            Instance::updateOrCreate(
                ['instance_external_id' => $serieId],
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