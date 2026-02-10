# SEGMETRE - Radiology Information System (RIS)

O **SEGMETRE** é uma plataforma robusta de Gerenciamento de Informações Radiológicas (RIS) desenvolvida em **Laravel 10.x**. O sistema foi projetado para otimizar o fluxo de trabalho em clínicas de radiologia, desde a captura de imagens DICOM até a entrega de laudos assinados e integração com sistemas de Saúde Ocupacional.



## 🚀 Tecnologias Principais

* **Framework:** Laravel 10.x com PHP 8.1+.
* **Frontend Reativo:** Livewire 3 e Alpine.js para interfaces dinâmicas.
* **Estilização:** Tailwind CSS.
* **Banco de Dados:** MySQL/PostgreSQL com suporte a logs de auditoria via Spatie Activitylog.
* **Integrações:** * **Orthanc:** Integração via API REST para sincronização de estudos DICOM.
    * **SOC:** Integração via SOAP/WSS para upload automático de laudos em PDF.

## 🛠️ Arquitetura do Sistema

### 1. Fluxo de Dados DICOM (Orthanc)
O sistema utiliza o `OrthancService` para realizar a comunicação com o servidor de imagens:
* **Sincronização:** Busca recursiva de Estudos, Séries e Instâncias.
* **Tratamento de Dados:** Limpeza de tags DICOM (como o separador `^` em nomes de pacientes) antes da persistência no banco local.

### 2. Gestão de Laudos
A emissão de laudos é automatizada pelo `LaudoService`:
* **Templates:** Utiliza arquivos `.docx` como base para preenchimento de placeholders.
* **Assinatura Digital:** Insere dinamicamente a imagem da assinatura do médico responsável.
* **Conversão:** Transforma o documento final em PDF para armazenamento e entrega.

### 3. Integração SOC (Saúde Ocupacional)
O `UploadLaudoSocService` gerencia o envio de documentos para o sistema SOC utilizando segurança avançada:
* **Autenticação WSS:** Geração de *Password Digest*, *Nonce* e *Timestamp* conforme padrão WS-Security.
* **Upload GED:** Envio de laudos vinculados ao código sequencial da ficha do funcionário.

## 📡 API Reference

O sistema disponibiliza endpoints protegidos por `API Bearer Token` para integração externa:

| Endpoint | Método | Descrição |
| :--- | :--- | :--- |
| `/api/exames` | `GET` | Lista exames com filtros de status. |
| `/api/exames/{id}` | `GET` | Detalhes completos de um estudo e suas séries. |
| `/api/exames/laudar/{id}` | `POST` | Processa e registra o laudo de uma série. |
| `/api/medico/cadastrar` | `POST` | Cadastro simplificado de profissionais médicos. |

## 🔐 Segurança e Níveis de Acesso

O acesso é controlado pelo middleware `CheckUserType`, que valida o perfil do usuário:

* **Admin/Dev:** Gestão de usuários, tokens de API e configurações de integração.
* **Médico:** Acesso à lista de exames pendentes e ferramentas de laudo.
* **Técnico:** Visualização e triagem de exames.
* **Paciente:** Acesso restrito via protocolo e senha para download de resultados.

## ⚙️ Configuração do Ambiente

1.  **Instalação:**
    ```bash
    composer install
    npm install && npm run build
    ```
2.  **Variáveis de Ambiente (`.env`):**
    * `ORTHANC_SERVER`: URL do servidor Orthanc.
    * `COD_EMPRESA_SOC`: Código identificador no sistema SOC.
3.  **Banco de Dados:**
    ```bash
    php artisan migrate --seed
    ```

## 📝 Regras de Negócio Importantes

* **Recálculo de Status:** O status de um estudo (`pendente`, `andamento`, `laudado`, `rejeitado`) é atualizado automaticamente sempre que uma série vinculada sofre alteração.
* **Vínculo Médico:** Ao cadastrar um usuário como "Médico", o sistema exige ou cria um perfil em `medicos_laudo` para gerenciar CRM e especialidade.
* **Auditoria:** Todas as ações críticas (edição de laudos, deleção de exames) são registradas para conformidade legal e médica.

---
Desenvolvido para **SEGMETRE - Radiology Information System**.