<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ExameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Dados de exemplo - depois pode ser substituído por consulta ao banco
        $exames = [
            [
                'id' => '1',
                'status' => 'urgente',
                'nome_paciente' => 'Maria Silva Santos',
                'data_exame' => '07/01/2026 14:30',
                'modalidade' => 'RX - Tórax PA',
                'medico_solicitante' => 'Dr. João Pereira'
            ],
            [
                'id' => '2',
                'status' => 'pendente',
                'nome_paciente' => 'José Carlos Oliveira',
                'data_exame' => '07/01/2026 13:15',
                'modalidade' => 'RX - Abdome',
                'medico_solicitante' => 'Dra. Ana Costa'
            ],
            [
                'id' => '3',
                'status' => 'laudado',
                'nome_paciente' => 'Pedro Henrique Lima',
                'data_exame' => '07/01/2026 10:00',
                'modalidade' => 'RX - Coluna Lombar',
                'medico_solicitante' => 'Dr. Carlos Mendes'
            ],
            [
                'id' => '4',
                'status' => 'pendente',
                'nome_paciente' => 'Fernanda Rodrigues',
                'data_exame' => '06/01/2026 16:45',
                'modalidade' => 'RX - Joelho D',
                'medico_solicitante' => 'Dr. João Pereira'
            ],
            [
                'id' => '5',
                'status' => 'rejeitado',
                'nome_paciente' => 'Lucas Martins',
                'data_exame' => '06/01/2026 15:20',
                'modalidade' => 'RX - Tórax AP',
                'medico_solicitante' => 'Dra. Silvane Andrade'
            ],
        ];

        // Aplicar filtro se houver
        $filtro = $request->get('filtro', 'todos');
        
        if ($filtro === 'pendentes') {
            $exames = array_values(array_filter($exames, fn($e) => $e['status'] === 'pendente'));
        } elseif ($filtro === 'urgentes') {
            $exames = array_values(array_filter($exames, fn($e) => $e['status'] === 'urgente'));
        } elseif ($filtro === 'meus') {
            $exames = array_values(array_filter($exames, fn($e) => $e['status'] !== 'laudado'));
        }

        return view('exames.index', compact('exames', 'filtro'));
    }
}

