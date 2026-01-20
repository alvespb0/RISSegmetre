<?php
namespace App\Services;

use Exception;

class DocxToPdfService
{   
    /**
     * Converte um arquivo DOCX em PDF usando o LibreOffice em modo headless.
     *
     * Observação: depende da variável de ambiente `LIBREOFFICE_BIN` apontando para o binário
     * do LibreOffice (especialmente importante no Windows).
     *
     * @param string $docxPath Caminho absoluto/relativo do arquivo .docx de entrada.
     * @return string Caminho do PDF gerado (mesma pasta do DOCX, com extensão .pdf).
     *
     * @throws \Exception Quando o DOCX não existe, o LibreOffice não é encontrado, a conversão falha
     *                   ou o PDF não é gerado.
     */
    public function convert(string $docxPath): string
    {
        if (!file_exists($docxPath)) {
            throw new Exception('Arquivo DOCX não encontrado');
        }

        $outputDir = dirname($docxPath);
        $libreOffice = env('LIBREOFFICE_BIN');

        if (!$libreOffice || !file_exists($libreOffice)) {
            throw new Exception('LibreOffice não encontrado: ' . $libreOffice);
        }

        $command = sprintf(
            '"%s" --headless --nologo --nofirststartwizard --convert-to pdf "%s" --outdir "%s"',
            $libreOffice,
            $docxPath,
            $outputDir
        );

        // ⚠️ Variáveis obrigatórias no Windows
        $env = [
            'HOME=' . sys_get_temp_dir(),
            'USERPROFILE=' . sys_get_temp_dir(),
            'TEMP=' . sys_get_temp_dir(),
        ];

        exec($command, $output, $code);

        \Log::error('LibreOffice CMD', [
            'cmd'    => $command,
            'code'   => $code,
            'output' => $output,
        ]);

        if ($code !== 0) {
            throw new Exception('Erro ao converter DOCX para PDF');
        }

        $pdfPath = str_replace('.docx', '.pdf', $docxPath);

        if (!file_exists($pdfPath)) {
            throw new Exception('PDF não foi gerado');
        }

        return $pdfPath;
    }


}

?>