<?php
namespace App\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use App\Models\Integracao;
use App\Models\Serie;

class UploadLaudoSocService
{

    /**
     * Construtor da classe UploadLaudoSocService
     * 
     * Inicializa o service com as credenciais de autenticação WSS e os dados
     * necessários para realizar o upload do GED do sistema SOC.
     * 
     * @param string $username Nome de usuário para autenticação WSS (ex: U3338099)
     * @param string $password Senha/chave para autenticação WSS
     * @param int $codSequencial Resgatado no inicio do fluxo
     * @param string $laudo_path path para resgatar o arquivo a ser feito o upload no SOC
     */
    public function __construct($username = null, $password = null, $codSequencial = null, $laudo_path = null){
        $this->username = $username;
        $this->password = $password;
        $this->codSequencial = $codSequencial;
        $this->laudo_path = $laudo_path;
    }

    public function requestUpload(){
        $endpoint = Integracao::where('slug', 'ws_soc_upload_ged')->first()->endpoint;

        $contentId = '1203851348728';
        $boundary  = 'uuid:' . Str::uuid();
        
        $fileStream = Storage::readStream($this->laudo_path);

        $xml = $this->buildEnvelope();

        $multipart = new MultipartStream([
            [
                'name' => 'root',
                'contents' => $xml,
                'headers' => [
                    'Content-Type' => 'application/xop+xml; charset=UTF-8; type="text/xml"',
                    'Content-Transfer-Encoding' => '8bit', // XML viaja melhor como 8bit
                    'Content-ID' => '<root.message@cxf.apache.org>',
                ],
            ],
            [
                'name' => 'file',
                'contents' => $fileStream, // Passamos a string binária direta
                'headers' => [
                    // MUDANÇA CRUCIAL: octet-stream evita que o servidor tente parser o PDF na entrada
                    'Content-Type' => 'application/pdf', 
                    'part' => '1203851348728',
                    'Content-Transfer-Encoding' => 'binary',
                    'Content-ID' => "<$contentId>",
                ],
            ],
        ], $boundary);
        
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 60,
        ]);

        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 
                        "multipart/related; ".
                        "type=\"application/xop+xml\"; ".
                        "start=\"<root.message@cxf.apache.org>\"; ".
                        "start-info=\"text/xml\"; ".
                        "boundary=\"$boundary\"",
                ],
                'body' => $multipart,
            ]);
            
            $responseBody = (string) $response->getBody();

            if (str_contains($responseBody, '<return>true</return>')) {
                return true;
            }

            \Log::error('Upload SOC respondeu sem confirmação.', ['body' => $responseBody]);
            return false;
        } catch (\Exception $e) {
            \Log::error('Erro na requisição Guzzle SOC', ['msg' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Constrói o envelope SOAP completo para a requisição
     * 
     * Monta o envelope SOAP com header (autenticação WSS) e body
     * (dados da requisição de upload) conforme o padrão SOAP 1.1.
     * 
     * @return string XML do envelope SOAP completo
     */
    private function buildEnvelope(): string{
        return trim(<<<XML
                    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.soc.age.com/">
                        <soapenv:Header>
                            {$this->buildHeaderWSS()}
                        </soapenv:Header>
                        <soapenv:Body>
                            {$this->buildBody()}
                        </soapenv:Body>
                    </soapenv:Envelope>
                    XML);
    }

    private function buildBody(): string{
        $codFicha = $this->codSequencial;
        $password = $this->password;
        $empresaPrincipal = ENV('COD_EMPRESA_SOC');
        $codigoResponsavel = ENV('COD_RESPONSAVEL_SOC');
        $codigoUsuario = ENV('COD_USUARIO_INTEGRA_SOC');
        $contentId = "1203851348728";
        $body = <<<XML
                    <ser:uploadArquivo xmlns:ser="http://services.soc.age.com/">
                        <arg0> 
                        <arquivo>
                        <xop:Include href="cid:$contentId"
                        xmlns:xop="http://www.w3.org/2004/08/xop/include"/>
                        </arquivo>
                        <classificacao>RESULTADO_EXAME</classificacao>
                            <codigoSequencialFicha>{$codFicha}</codigoSequencialFicha>
                            <extensaoArquivo>PDF</extensaoArquivo>
                            <identificacaoVo>
                                <chaveAcesso>{$password}</chaveAcesso>
                                <codigoEmpresaPrincipal>{$empresaPrincipal}</codigoEmpresaPrincipal>
                                <codigoResponsavel>{$codigoResponsavel}</codigoResponsavel>
                                <codigoUsuario>{$codigoUsuario}</codigoUsuario>
                            </identificacaoVo>
                            <nomeArquivo>Laudo-RX</nomeArquivo>
                        </arg0>
                    </ser:uploadArquivo>
                    XML;
        return $body;
    }

    /**
     * Constrói o header de segurança WSS (WS-Security) para autenticação SOAP
     * 
     * Monta o XML do header de segurança contendo timestamp, username token,
     * password digest, nonce e created conforme o padrão WS-Security 1.0.
     * Este header é necessário para autenticar a requisição SOAP no sistema SOC.
     * 
     * @return string XML do header de segurança WSS
     */
    private function buildHeaderWSS(): string{
        $wss = $this->buildWSS();

        return <<<XML
        <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
            <wsu:Timestamp wsu:Id="Timestamp-1">
                <wsu:Created>{$wss['created']}</wsu:Created>
                <wsu:Expires>{$wss['expires']}</wsu:Expires>
            </wsu:Timestamp>
            <wsse:UsernameToken wsu:Id="UsernameToken-1">
                <wsse:Username>{$wss['username']}</wsse:Username>
                <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">{$wss['digest']}</wsse:Password>
                <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">{$wss['nonce']}</wsse:Nonce>
                <wsu:Created>{$wss['created']}</wsu:Created>
            </wsse:UsernameToken>
        </wsse:Security>
        XML;
    }

    /**
     * Gera os dados de autenticação WSS (WS-Security)
     * 
     * Cria os componentes necessários para autenticação WSS conforme o padrão:
     * - NONCE: Bytes aleatórios (16 bytes) codificados em Base64
     * - TIMESTAMP: Data de criação e expiração com precisão de milissegundos
     * - PASSWORD DIGEST: Hash SHA1(nonce_bytes + created_string + password) em Base64
     * 
     * O timestamp é gerado com precisão de milissegundos (3 casas decimais) conforme
     * especificação do padrão WS-Security.
     * 
     * @param int $ttlSeconds Tempo de vida do token em segundos (padrão: 60 segundos)
     * @return array Array associativo contendo:
     *               - 'username': Nome de usuário
     *               - 'digest': Password digest em Base64
     *               - 'nonce': Nonce em Base64
     *               - 'created': Timestamp de criação (formato ISO 8601 com milissegundos)
     *               - 'expires': Timestamp de expiração (formato ISO 8601 com milissegundos)
     */
    private function buildWSS(int $ttlSeconds = 100): array
    {
        // 1. NONCE
        // O padrão exige que geremos bytes aleatórios.
        // Para o XML, enviamos em Base64. Para o Hash, usamos os bytes brutos.
        $nonceBytes = random_bytes(16);
        $nonceBase64 = base64_encode($nonceBytes);

        // 2. TIMESTAMP (O Pulo do Gato)
        // Precisamos de milissegundos exatos (3 casas), como na página 9 da doc.
        // O microtime(true) retorna o timestamp com decimais.
        $t = microtime(true);
        $micro = sprintf("%03d", ($t - floor($t)) * 1000);
        
        // Data de Criação (Created)
        $created = gmdate('Y-m-d\TH:i:s', (int)$t) . '.' . $micro . 'Z';
        
        // Data de Expiração (Expires) - Recomendado 1 minuto na página 9 [cite: 187]
        $expires = gmdate('Y-m-d\TH:i:s', (int)$t + $ttlSeconds) . '.' . $micro . 'Z';

        // 3. PASSWORD DIGEST
        // A fórmula é: SHA1( NonceBytes + CreatedString + Senha )
        // A senha é a chave '6f16...' que você recebeu, usada LIMPA aqui.
        $passwordClean = trim($this->password);
        
        $digest = base64_encode(
            sha1($nonceBytes . $created . $passwordClean, true)
        );

        return [
            'username' => $this->username, // Ex: U3338099
            'digest'   => $digest,
            'nonce'    => $nonceBase64,
            'created'  => $created,
            'expires'  => $expires,
        ];
    }

    public function uploadFromSerie($serieId){
        try {

            $serie = Serie::findOrFail($serieId);

            $laudo = $serie->laudo()
                ->where('ativo', true)
                ->first();

            if (!$laudo || !$laudo->laudo_path) {
                \Log::warning('SOC upload abortado: laudo não encontrado', [
                    'serie_id' => $serieId
                ]);
                return false;
            }

            $integracao = Integracao::where('slug', 'ws_soc_upload_ged')->first();

            if (!$integracao) {
                \Log::error('Integração SOC não configurada');
                return false;
            }

            $service = new self(
                $integracao->username,
                $integracao->getDecryptedPassword(),
                $serie->study->cod_sequencial_ficha,
                $laudo->laudo_path
            );

            $success = $service->requestUpload();

            if ($success) {

                $serie->update([
                    'enviado_soc' => true,
                ]);

                \Log::info('Upload SOC realizado com sucesso', [
                    'serie_id' => $serieId
                ]);

                return true;
            }

            \Log::warning('SOC upload retornou falso', [
                'serie_id' => $serieId
            ]);

            return false;

        } catch (\Throwable $e) {

            \Log::error('Erro no upload SOC', [
                'serie_id' => $serieId,
                'erro' => $e->getMessage()
            ]);

            return false;
        }
    }

}