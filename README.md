# SEGMETRE - Radiology Information System (RIS)

O **RIS** é uma plataforma robusta de Gerenciamento de Informações Radiológicas (RIS) desenvolvida em **Laravel 10.x**. O sistema foi projetado para otimizar o fluxo de trabalho em clínicas de radiologia, desde a captura de imagens DICOM até a entrega de laudos assinados e integração com sistemas de Saúde Ocupacional.

## 🚀 Tecnologias Principais e Dependências

- **Framework:** Laravel 10.x com PHP 8.1+.
- **Frontend Reativo:** Livewire 3 e Alpine.js para interfaces dinâmicas.
- **Estilização:** Tailwind CSS.
- **Banco de Dados:** MySQL/PostgreSQL com suporte a logs de auditoria via Spatie Activitylog.
- **Dependência de Sistema:** **LibreOffice** (Obrigatório no servidor para conversão de laudos `.docx` para `.pdf` via CLI).
- **Integrações:**
  - **Orthanc:** Integração via API REST para sincronização de estudos DICOM.
  - **SOC:** Integração via SOAP/WSS para upload automático de laudos em PDF.

---

# 🛠️ Arquitetura do Sistema

## 1. Fluxo de Dados DICOM (Orthanc)

O sistema utiliza o `OrthancService` para realizar a comunicação com o servidor de imagens:

- **Sincronização:** Busca recursiva de Estudos, Séries e Instâncias (executada em background via CRON/Jobs).
- **Tratamento de Dados:** Limpeza de tags DICOM (como o separador `^` em nomes de pacientes) antes da persistência no banco local.

## 2. Gestão de Laudos

A emissão de laudos é automatizada pelo `LaudoService`:

- **Templates:** Utiliza arquivos `.docx` como base para preenchimento de placeholders.
- **Assinatura Digital:** Insere dinamicamente a imagem da assinatura do médico responsável.
- **Conversão (Atenção Infra):** Transforma o documento final em PDF utilizando a função `exec()` do PHP para invocar o `soffice` (LibreOffice).

## 3. Integração SOC (Saúde Ocupacional)

O `UploadLaudoSocService` gerencia o envio de documentos para o sistema SOC utilizando segurança avançada:

- **Autenticação WSS:** Geração de *Password Digest*, *Nonce* e *Timestamp* conforme padrão WS-Security.
- **Upload GED:** Envio de laudos vinculados ao código sequencial da ficha do funcionário via Filas (Jobs).

---

# 📡 API Reference

O sistema disponibiliza endpoints protegidos por `API Bearer Token` para integração externa.

> **Nota de Arquitetura:** A API utiliza uma implementação customizada de tokens (`ApiToken`) vinculada diretamente às entidades de `Empresa`, garantindo isolamento de dados (Multi-tenancy) por inquilino.

| Endpoint | Método | Descrição |
|----------|--------|-----------|
| `/api/exames` | `GET` | Lista exames com filtros de status. |
| `/api/exames/{id}` | `GET` | Detalhes completos de um estudo e suas séries. |
| `/api/exames/laudar/{id}` | `POST` | Processa e registra o laudo de uma série. |
| `/api/medico/cadastrar` | `POST` | Cadastro simplificado de profissionais médicos. |

Para mais informações sobre os endpoints, consulte a documentação.
---

# 🔐 Segurança e Níveis de Acesso

O acesso é controlado pelo middleware `CheckUserType`, que valida o perfil do usuário:

- **Admin/Dev:** Gestão de usuários, tokens de API e configurações de integração.
- **Médico:** Acesso à lista de exames pendentes e ferramentas de laudo.
- **Técnico:** Visualização e triagem de exames.
- **Paciente:** Acesso restrito via protocolo e senha para download de resultados.

---

# ⚙️ Configuração do Ambiente

## 1. Requisitos de Servidor

Certifique-se de ter o PHP 8.1+, Composer, Node.js/NPM e o **LibreOffice** instalados no SO.

## 2. Instalação

```bash
composer install
npm install && npm run build
```

## 3. Variáveis de Ambiente (`.env`)

- `ORTHANC_SERVER`: URL do servidor Orthanc.
- `COD_EMPRESA_SOC`: Código identificador no sistema SOC.

## 4. Banco de Dados e Storage

```bash
php artisan migrate --seed
php artisan storage:link
```

## 5. Processos em Background (Obrigatório para Produção)

### Agendador (CRON)

Adicione a seguinte entrada no crontab do servidor para sincronização automática:

```
* * * * * cd /caminho-do-projeto && php artisan schedule:run >> /dev/null 2>&1
```

### Workers (Filas)

Configure o Supervisor (ou similar) para manter os processos de fila rodando:

```bash
php artisan queue:work --tries=3
```

---

# 📝 Regras de Negócio Importantes

- **Recálculo de Status:** O status de um estudo (`pendente`, `andamento`, `laudado`, `rejeitado`) é atualizado automaticamente sempre que uma série vinculada sofre alteração.
- **Vínculo Médico:** Ao cadastrar um usuário como "Médico", o sistema exige ou cria um perfil em `medicos_laudo` para gerenciar CRM e especialidade.
- **Auditoria:** Todas as ações críticas (edição de laudos, deleção de exames, downloads sensíveis) são registradas no log para conformidade legal (LGPD) e médica.

---

# ✅ Checklist de Deploy (Produção)

Antes de realizar o deploy em ambiente de produção, certifique-se de validar os seguintes pontos:

## Fase 1: Código e Segurança

- [ ] **Guzzle SSL:** O serviço `UploadLaudoSocService` deve ter a verificação de certificado habilitada (`'verify' => true`).
- [ ] **Validação Livewire:** Componentes como `SeriesList` possuem checagem de existência do relacionamento `$medico` antes de persistir ações críticas.
- [ ] **Segurança de Download:** Políticas de acesso (Policies) aplicadas nas rotas de download de exames/laudos para evitar IDOR, além da criptografia de IDs.

## Fase 2: Infraestrutura

- [ ] **HTTPS:** Certificado SSL configurado corretamente no Nginx/Apache.
- [ ] **LibreOffice:** Pacote instalado no SO e acessível globalmente via PATH para o PHP.
- [ ] **Permissões:** Diretórios `storage/` e `bootstrap/cache/` com permissão de escrita para o usuário do servidor web (ex: `www-data`).

## Fase 3: Otimização Laravel

Executar comandos de cache para otimização de performance:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

- [ ] **Supervisor & Cron:** Serviços devidamente configurados e rodando para garantir envios ao SOC e buscas no Orthanc sem onerar a requisição HTTP do usuário.

---

Desenvolvido para **SEGMETRE - Radiology Information System**.