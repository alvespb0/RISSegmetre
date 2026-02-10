<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\View\View;
use App\Models\Study;
use App\Models\Serie;
use Carbon\Carbon;

/**
 * Controller responsável pelo Dashboard do sistema.
 *
 * Centraliza a coleta de métricas, estatísticas e dados agregados
 * relacionados a exames, laudos e desempenho geral.
 *
 * OBS: Algumas métricas ainda utilizam dados hardcoded
 * enquanto o modelo final de negócio está em construção.
 */
class DashboardController extends Controller
{
    /**
     * Exibe a página principal do dashboard.
     *
     * Coleta estatísticas gerais, dados temporais, distribuição por status,
     * partes examinadas e métricas de performance para renderização
     * dos gráficos e cards do dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $stats = $this->getNumExames();

        $examesTempo = $this->getNumExamesDia();

        $examesStatus = [
            'labels' => ['Laudados', 'Pendentes', 'Rejeitados'],
            'data' => [$this->getNumLaudados(), $this->getNumPendentes(), $this->getNumRejeitados()],
        ];

        $examesBodyPartExamined = $this->examesPorParte();

        // Dados para o gráfico de Performance de Laudos
        $performanceLaudos = $this->getNumExamesRejeitadosMes();

        return view('dashboard', compact(
            'stats',
            'examesTempo',
            'examesStatus',
            'examesBodyPartExamined',
            'performanceLaudos'
        ));
    }
    
    private function getNumExamesRejeitadosMes(): array{
        $rejeitados = Study::query()
            ->selectRaw('YEAR(study_date) as ano')
            ->selectRaw('MONTH(study_date) as mes')
            ->selectRaw('COUNT(*) as total')
            ->whereHas('serie.instance', fn ($q) =>
                $q->where('status', 'rejeitado')
            )
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        return [
            'labels' => $rejeitados->map(
                fn ($item) => sprintf('%02d/%d', $item->mes, $item->ano)
            ),
            'data' => $rejeitados->pluck('total')
        ];
    }
    /**
     * Retorna a quantidade de exames por dia (baseado na data do estudo),
     * incluindo o total geral e o total de exames laudados.
     *
     * Utilizado para gráficos temporais (linha / área).
     *
     * @return array{
     *     labels: \Illuminate\Support\Collection,
     *     data: \Illuminate\Support\Collection,
     *     laudados: \Illuminate\Support\Collection
     * }
     */
    private function getNumExamesDia(): array{
        $result = Study::selectRaw('study_date, count(*) as total')
            ->groupBy('study_date')
            ->orderBy('study_date')
            ->get();
            
        $laudados = Study::selectRaw('study_date, count(*) as total')
            ->whereHas('serie.instance', function ($q) {
                $q->where('status', 'laudado');
            })
            ->groupBy('study_date')
            ->orderBy('study_date')
            ->get();

        return [
            'labels' => $result->pluck('study_date'),
            'data'   => $result->pluck('total'),
            'laudados' => $laudados->pluck('total')
        ];
    }

    /**
     * Retorna a distribuição de exames por parte do corpo examinada.
     *
     * Utilizado para gráficos de pizza ou barras.
     *
     * @return array{
     *     labels: \Illuminate\Support\Collection,
     *     data: \Illuminate\Support\Collection
     * }
     */
    private function examesPorParte(): array{
        $resultado = Serie::query()
            ->selectRaw('body_part_examined, COUNT(*) as total')
            ->groupBy('body_part_examined')
            ->orderBy('body_part_examined')
            ->get();

        return [
            'labels' => $resultado->pluck('body_part_examined')->values(),
            'data'   => $resultado->pluck('total')->values(),
        ];
    }
 
    /**
     * Retorna os principais indicadores numéricos do dashboard.
     *
     * Inclui exames pendentes, exames do dia, laudos concluídos
     * e exames rejeitados.
     *
     * @return array{
     *     exames_pendentes: int,
     *     exames_hoje: int,
     *     laudos_concluidos: int,
     *     exames_rejeitados: int
     * }
     */
    private function getNumExames(): array{
        $examesPendentes = $this->getNumPendentes();

        $examesHoje = Study::where('study_date', now()->toDateString())->count();

        $examesLaudados = $this->getNumLaudados();

        $examesRejeitados = $this->getNumRejeitados();

        $stats = [
            'exames_pendentes' => $examesPendentes,
            'exames_hoje' => $examesHoje,
            'laudos_concluidos' => $examesLaudados,
            'exames_rejeitados' => $examesRejeitados,
        ];

        return $stats;
    }

    /**
     * Retorna o número de exames com instâncias em status "pendente".
     *
     * @return int
     */
    private function getNumPendentes(): int{
        return Study::with(['serie.instance'])->whereHas('serie.instance', function($q){
            $q->where('status', 'pendente');
        })->count();
    }

    /**
     * Retorna o número de exames com instâncias em status "laudado".
     *
     * @return int
     */
    private function getNumLaudados(): int{
        return Study::with(['serie.instance'])->whereHas('serie.instance', function($q){
            $q->where('status', 'laudado');
        })->count();
    }

    /**
     * Retorna o número de exames com instâncias em status "rejeitado".
     *
     * @return int
     */
    private function getNumRejeitados(){
        return Study::with(['serie.instance'])->whereHas('serie.instance', function($q){
            $q->where('status', 'rejeitado');
        })->count();
    }
}

