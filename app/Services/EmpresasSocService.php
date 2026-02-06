<?php
namespace App\Services;

use App\Models\EmpresasSoc;
use App\Models\Integracao;
use App\Models\Study;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EmpresasSocService
{
    /**
     * Busca e atualiza as empresas cadastradas no SOC através da API
     * 
     * Realiza uma requisição GET para o endpoint configurado na integração,
     * converte a resposta para UTF-8 para preservar caracteres especiais,
     * e salva/atualiza os registros no banco de dados.
     * 
     * @return bool|string Retorna true em caso de sucesso ou uma string com mensagem de erro
     */
    public function getEmpresasSoc(){
        $integracao = Integracao::where('slug', 'ws_soc_empresas_cadastradas')->first();

        $codEmpresaPrincipal = ENV('COD_EMPRESA_SOC');

        try{
            $jsonString = "{\"empresa\":\"$codEmpresaPrincipal\",\"codigo\":\"211287\",\"chave\":\"{$integracao->getDecryptedPassword()}\",\"tipoSaida\":\"json\"}";
            
            \Log::info('Preparando para requisitar as empresas cadastradas no SOC ', ['string da requisição' => $jsonString]);

            $response = Http::get($integracao->endpoint, [
                'parametro' => $jsonString
            ]);

            if($response->ok()){
                $body = $response->body();
                $bodyUtf8 = $this->convertToUtf8($body);

                $dados = json_decode($bodyUtf8, true);
                if(empty($dados)){
                    \Log::error('Não localizado empresas pela api de exporta dados do SOC');
                    return null;
                }

                foreach($dados as $dado){
                    $cnpj = preg_replace('/\D/', '', $dado['CNPJ']);
                    EmpresasSoc::updateOrCreate(
                        ['codigo_soc' => $dado['CODIGO']],
                        [
                            'nome' => $dado['RAZAOSOCIAL'],
                            'cnpj' => $cnpj
                        ]
                    );
                }
            }

            \Log::info('Finalizado Busca de empresas cadastradas no SOC');
            
            return true;
        }catch (\Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }

    /**
     * Converte uma string para UTF-8, preservando caracteres especiais
     * 
     * Tenta detectar o encoding atual e converte para UTF-8.
     * Se a detecção falhar, tenta converter de ISO-8859-1 para UTF-8.
     * 
     * @param string $string String a ser convertida
     * @return string String convertida para UTF-8
     */
    private function convertToUtf8(string $string): string
    {
        // Detecta o encoding atual
        $encoding = mb_detect_encoding($string, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        
        // Se já está em UTF-8, retorna como está
        if ($encoding === 'UTF-8') {
            return $string;
        }
        
        // Converte para UTF-8
        return mb_convert_encoding($string, 'UTF-8', $encoding ?: 'ISO-8859-1');
    }

    /**
     * Consulta o SOC para resgatar o código sequencial da ficha
     * relacionado ao estudo informado.
     *
     * O método monta os parâmetros necessários para a integração externa,
     * realiza a chamada HTTP ao endpoint configurado e retorna o código
     * sequencial encontrado.
     *
     * Fluxo:
     * - Calcula o período de busca baseado na data do estudo
     * - Monta payload de integração
     * - Realiza requisição HTTP ao SOC
     * - Converte resposta para UTF-8
     * - Extrai o código sequencial retornado
     *
     * Em caso de falha (HTTP, resposta vazia ou exceção),
     * retorna status=false com mensagem descritiva.
     *
     * @param string|int $codEmpresa Código da empresa no SOC.
     * @param Study $study Instância do estudo contendo dados do paciente e data do exame.
     *
     * @return array{
     *     status: bool,
     *     codSequencial?: string,
     *     message: string
     * }
     *
     * @throws \Exception Exceções internas são capturadas e logadas,
     * mas não propagadas para fora do método.
     */
    public function getCodSequencial($codEmpresa, Study $study): array{
        $integracao = Integracao::where('slug', 'ws_soc_resgata_cod_ficha')->first();

        $codPrestador = ENV('COD_PRESTADOR_SOC');

        $dataEstudo = Carbon::parse($study->study_date);

        $dataFim = $dataEstudo->format('d/m/Y');
        $dataInicio = $dataEstudo->copy()->subWeek()->format('d/m/Y');

        try{
            $parametro = [
                "empresa" => $codEmpresa,
                "codigo" => "212068",
                "chave" => $integracao->getDecryptedPassword(),
                "tipoSaida" => "json",
                "funcionarioInicio" => "0",
                "funcionarioFim" => "99999",
                "paramData" => "1",
                "dataInicio" => $dataInicio,
                "dataFim" => $dataFim,
                "paramFunc" => "1",
                "cpffuncionario" => $study->patient->patient_cpf,
                "codpresta" => $codPrestador,
                "paramPresta" => "1"
            ];

            $json = json_encode($parametro, JSON_UNESCAPED_SLASHES);

            \Log::info('Preparando para resgatar o codigo sequencial da ficha.',[
                'study' => $study->id,
                'query_string' => $json
            ]);

            $response = Http::get($integracao->endpoint, [
                'parametro' => $json
            ]);

            if($response->ok()){
                $body = $response->body();

                $bodyUtf8 = $this->convertToUtf8($body);

                $dados = json_decode($bodyUtf8, true);
                
                var_dump($dados);

                if(empty($dados)){
                    \Log::error('Não localizado codigo sequencial para query string informada');
                    return [
                        'status' => false,
                        'message' => 'Não localizado codigo sequencial para query string informada'
                    ];
                }

                \Log::info('Finalizado Busca por código sequencial');

                return [
                    'status' => true,
                    'codSequencial' => $dados[0]['SEQUENCIAFICHA'],
                    'message' => 'Registro SOC vinculado com sucesso'
                ];
            }else{
                \Log::error('Erro HTTP SOC', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'status' => false,
                    'message' => 'Erro na comunicação com o SOC'
                ];
            }
        }catch (\Exception $e) {
            \Log::error(['erro' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Erro ao vincular registro SOC',
            ];
        }
    }
}