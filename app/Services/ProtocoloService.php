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
use chillerlan\QRCode\Data\QRMatrix;

class ProtocoloService
{
    /**
     * Gera um protocolo de entrega para a Série (com QR Code), cria o PDF via template
     * e persiste o registro em `DeliveryProtocol`.
     *
     * @param \App\Models\Serie $serie Série vinculada ao protocolo de entrega.
     *
     * @return array{pdf:string} Array com o caminho do PDF gerado.
     *
     * @throws \Exception Quando o template não é encontrado ou quando há falha na conversão/geração do PDF.
     */
    public function gerarProtocolo(Serie $serie)
    {
        // 1. Lógica do Protocolo
        do {
            $protocolo = Str::upper(Str::random(16));
        } while (DeliveryProtocol::where('protocolo', $protocolo)->exists());

        $senhaPlana = Str::random(8);
        $url = env('APP_URL');
        $urlProtocol = $url.'/login/patient/protocolo-entrega'.'/' . $protocolo;

        // 2. Preparar Diretórios
        $uuid = Str::uuid()->toString();
        $tmpDir = storage_path("app/tmp/processamento/{$uuid}");

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $qrCodePath = "{$tmpDir}/qrcode.png";
        
        $options = new QROptions([
            'version'          => QRCode::VERSION_AUTO,
            'outputType'       => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'         => QRCode::ECC_Q,
            'scale'            => 10,
            'imageBase64'      => false,
            'imageTransparent' => false, 
            'bgColor'          => [255, 255, 255],     
            'addQuietzone'     => true,
            'quietzoneSize'    => 4,
        ]);

        $qrcode = new QRCode($options);
        $qrBinary = $qrcode->render($urlProtocol);
        file_put_contents($qrCodePath, $qrBinary);

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
        $template->setValue('url', $url);

        // Insere a imagem (Agora é um PNG real e válido)
        $template->setImageValue('qrCode', [
            'path'   => $qrCodePath,
            'width'  => 150,
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
     * Remove recursivamente um diretório temporário e seus arquivos.
     *
     * @param mixed $dir Caminho do diretório a ser removido.
     * @return void
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