<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        // Dados para os cards de estatísticas
        $stats = [
            'exames_pendentes' => 24,
            'exames_hoje' => 48,
            'laudos_concluidos' => 156,
            'urgentes' => 3,
        ];

        // Dados para o gráfico de Exames e Laudos - Última Semana
        $examesTempo = [
            'labels' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            'exames' => [42, 38, 45, 52, 48, 35, 28],
            'laudos' => [38, 35, 42, 48, 45, 32, 25],
        ];

        // Dados para o gráfico de Distribuição por Status
        $examesStatus = [
            'labels' => ['Laudados', 'Pendentes', 'Urgentes', 'Rejeitados'],
            'data' => [156, 24, 3, 5],
        ];

        // Dados para o gráfico de Exames por Modalidade
        $examesModalidade = [
            'labels' => ['RX Tórax', 'RX Coluna', 'RX Abdome', 'RX Extremidades', 'USG', 'TC', 'RM'],
            'data' => [45, 32, 28, 22, 35, 18, 12],
        ];

        // Dados para o gráfico de Performance de Laudos
        $performanceLaudos = [
            'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'data' => [4.2, 3.8, 3.5, 3.2, 2.9, 2.7, 2.5, 2.4, 2.3, 2.2, 2.1, 2.0],
        ];

        return view('dashboard', compact(
            'stats',
            'examesTempo',
            'examesStatus',
            'examesModalidade',
            'performanceLaudos'
        ));
    }
}

