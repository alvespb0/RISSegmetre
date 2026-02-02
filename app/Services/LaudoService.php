<?php
namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Serie;

/**
 * Serviço responsável por gerar e armazenar o laudo (DOCX/PDF) de uma Série.
 *
 * Fluxo geral:
 * - Carrega um template DOCX e preenche os placeholders com dados da Série/Paciente;
 * - Opcionalmente insere a imagem de assinatura do médico;
 * - Converte o DOCX para PDF via `DocxToPdfService`;
 * - Move o PDF para o diretório final e retorna o caminho gerado.
 */
class LaudoService
{
    /**
     * Gera o laudo da Série a partir de template, converte para PDF e retorna o caminho final.
     *
     * @param \App\Models\Serie $serie Série para a qual o laudo será gerado.
     * @param mixed $laudo Texto do laudo (conteúdo a ser inserido no template).
     *
     * @return array{pdf:string} Array com o caminho do PDF gerado.
     */
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

        if (!empty($serie->laudo_path)) {
            Storage::delete($serie->laudo_path);
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

        $storagePath = 'laudos/laudo_' . $serie->id . '_' . now()->timestamp . '.pdf';

        // salva usando o Storage
        Storage::put($storagePath, file_get_contents($pdfPath));

        // limpeza
        @unlink($docxPath);
        @unlink($pdfPath);
        @rmdir($tmpDir);

        return [
            'pdf' => $storagePath
        ];
    }
}

?>