<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Integracao;

class IntegracoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para upload de SOCGED',
            'endpoint' => 'https://ws1.soc.com.br/WSSoc/services/UploadArquivosWs',
            'slug' => 'ws_soc_upload_ged',
            'auth' => 'wss',
            'tipo' => 'soap'
        ]);

        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para cadastro de empresas',
            'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
            'slug' => 'ws_soc_empresas_cadastradas',
            'auth' => 'bearer',
            'tipo' => 'rest'
        ]);

        Integracao::create([
            'sistema' => 'SOC',
            'descricao' => 'WS Soc para resgatar o codigo sequencial da ficha',
            'endpoint' => 'https://ws1.soc.com.br/WebSoc/exportadados',
            'slug' => 'ws_soc_resgata_cod_ficha',
            'auth' => 'bearer',
            'tipo' => 'rest'
        ]);

    }
}
