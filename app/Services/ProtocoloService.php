<?php

namespace App\Services;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Serie;
use App\Models\DeliveryProtocol;

// Importações da nova biblioteca
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Hash;

class ProtocoloService
{
    public function gerarProtocolo(Serie $serie)
    {
        // 1. Lógica do Protocolo
        do {
            $protocolo = Str::upper(Str::random(16));
        } while (DeliveryProtocol::where('protocolo', $protocolo)->exists());

        $senhaPlana = Str::random(8);
        $urlProtocol = env('APP_URL') . '/protocolo-entrega'.'/' . $protocolo;

        // 2. Preparar Diretórios
        $uuid = Str::uuid()->toString();
        $tmpDir = storage_path("app/tmp/processamento/{$uuid}");

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $qrCodePath = "{$tmpDir}/qrcode.png";
        
        $options = new QROptions([
            'version'      => 5, 
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG, // Força saída PNG
            'eccLevel'     => QRCode::ECC_L, 
            'scale'        => 10,   // Tamanho dos "pixels" (aumenta a resolução)
            'imageBase64'  => false, // Queremos os dados binários, não base64
        ]);

        // Renderiza e salva o arquivo diretamente
        $qrcode = new QRCode($options);
        $qrcode->render($urlProtocol, $qrCodePath);

        // ================= DADOS =================
        $paciente  = $serie->study->patient->nome ?? 'N/A';
        $exame     = 'Raio X ' . ($serie->body_part_examined ?? 'Geral');
        $dataExame = $serie->study->study_date ?? now()->format('Y-m-d');

        // ================= TEMPLATE WORD =================
        $templatePath = storage_path('app/modelos/Modelo-Protocolo.docx');
        
        if (!file_exists($templatePath)) {
             $this->cleanup($tmpDir);
             throw new \Exception("Template não encontrado: $templatePath");
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('paciente', $paciente);
        $template->setValue('exame', $exame);
        $template->setValue('dtExame', $dataExame);
        $template->setValue('protocolo', $protocolo);
        $template->setValue('senha', $senhaPlana);

        // Insere a imagem (Agora é um PNG real e válido)
        $template->setImageValue('qrCode', [
            'path'   => $qrCodePath,
            'width'  => 150,
            'height' => 150,
            'ratio'  => true,
        ]);

        // ================= SALVAR E CONVERTER =================
        $docxPath = "{$tmpDir}/protocolo.docx";
        $template->saveAs($docxPath);

        // Converte para PDF
        $pdfPath = app(DocxToPdfService::class)->convert($docxPath);
        
        // Pausa técnica para Windows liberar arquivos
        usleep(500000); 

        // Mover para pasta final
        $finalDir = storage_path('app/protocolos');
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $finalPdf = $finalDir . '/protocolo_' . $protocolo . '_' . now()->timestamp . '.pdf';

        if (file_exists($pdfPath)) {
            rename($pdfPath, $finalPdf);
        } else {
            throw new \Exception("Erro na conversão do PDF.");
        }

        // Limpeza
        $this->cleanup($tmpDir);

        DeliveryProtocol::create([
            'laudo_id' => $serie->id,
            'protocolo' => $protocolo,
            'senha' => Hash::make($senhaPlana),
            'protocolo_path' => $finalPdf
        ]);

        return [
            'pdf' => $finalPdf
        ];
    }

    /**
     * Função auxiliar para apagar pasta temporária
     */
    private function cleanup($dir)
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->cleanup("$dir/$file") : @unlink("$dir/$file");
        }
        @rmdir($dir);
    }
}