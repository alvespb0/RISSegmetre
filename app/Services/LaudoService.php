<?php
namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Serie;

/**
 * Serviço responsável pela integração com o servidor DICOM Orthanc.
 * * Realiza o consumo da API REST do Orthanc para sincronizar Pacientes, 
 * Estudos, Séries e Instâncias com o banco de dados local.
 */
class LaudoService
{
    public function gerarLaudo(Serie $serie, $laudo){
        $templatePath = storage_path('app/modelos/Modelo-Laudo-RX.docx');

        $template = new TemplateProcessor($templatePath);

        $paciente = $serie->study->patient->nome;
        $idade = $serie->study->patient->birth_date;
        $solicitante = $serie->study->solicitante;
        $aniversario = $serie->study->patient->birth_date;
        $dataExame = $serie->study->study_date;
        $exame = 'Raio X '.$serie->body_part_examined;
        $nomeMedicoRx = $serie->medico->name;

        $template->setValue('nomePaciente', $paciente);
        $template->setValue('idadePaciente', $idade);
        $template->setValue('medicoSolicitante', $solicitante);
        $template->setValue('dtNascimentoPaciente', $aniversario);
        $template->setValue('dtExame', $dataExame);
        $template->setValue('nomeExame', $exame);
        $template->setValue('textoLaudo', $laudo);
        $template->setValue('nomeMedicoRx', $nomeMedicoRx);
        
        $medico = $serie->medico;
        if ($medico && !empty($medico->signature_path)) {
            $assinaturaPath = storage_path('app/' . $medico->signature_path);
            
            if (file_exists($assinaturaPath)) {
                $template->setImageValue('assinatura', [
                    'path'   => $assinaturaPath,
                    'width'  => 325,  // largura em pixels
                    'ratio'  => true, // mantém proporção
                ]);
            } else {
                // Caso o arquivo não exista, coloca placeholder
                $template->setValue('assinatura', '');
            }
        } else {
            // Médico sem assinatura
            $template->setValue('assinatura', '');
        }

        if (!empty($serie->file_path)) {
            $oldPath = storage_path('app/' . ltrim($serie->file_path, '/'));

            $laudosDir = realpath(storage_path('app/laudos'));
            $oldReal   = realpath($oldPath);

            if ($oldReal && str_starts_with($oldReal, $laudosDir) && file_exists($oldReal)) {
                @unlink($oldReal);
            } else {
                Log::warning('Tentativa de remoção de laudo fora do diretório esperado', [
                    'serie_id' => $serie->id,
                    'path' => $serie->file_path,
                ]);
            }
        }

        $uuid = Str::uuid()->toString();
        $tmpDir = storage_path("app/tmp/laudos/{$uuid}");

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $docxPath = "{$tmpDir}/laudo.docx";
        $template->saveAs($docxPath);
        $pdfPath = app(DocxToPdfService::class)->convert($docxPath);
        
        sleep(1);

        $finalDir = storage_path('app/laudos');
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $finalPdf = $finalDir . '/laudo_' . $serie->id . '_' . now()->timestamp . '.pdf';

        rename($pdfPath, $finalPdf);

        @unlink($docxPath);
        @rmdir($tmpDir);

        return [
            'pdf' => $finalPdf
        ];
    }
}

?>