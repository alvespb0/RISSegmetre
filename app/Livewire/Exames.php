<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Study;
use App\Models\Instance;

class Exames extends Component
{
    use WithPagination;

    public $filtro = 'pendente';
    public array $openStudies = [];

    
    /**
     * Define o filtro de status usado para listar exames/instâncias e reseta a paginação.
     *
     * @param mixed $filtro Valor do filtro (ex.: 'pendente', 'laudado', 'rejeitado', 'todos').
     * @return void
     */
    public function setFiltro($filtro){
        $this->filtro = $filtro;
        $this->resetPage();
    }

    /**
     * Alterna (abre/fecha) o estado de expansão de um Study na lista.
     *
     * @param int $studyId ID do Study.
     * @return void
     */
    public function toggleStudy(int $studyId)
    {
        $this->openStudies[$studyId] =
            !($this->openStudies[$studyId] ?? false);
    }

    public function getExames(){
        $service = new \App\Services\OrthancService;

        $retorno = $service->getStudies();

        if(!$retorno){
            \Log::error('Erro ao resgatar novos exames');
            $this->dispatch('toast-error', message: 'Erro ao capturar novos exames');
        }

        $this->dispatch('toast-success', message: 'Exames atualizados!');
    }
    /**
     * Monta os dados para a view do componente, aplicando filtro e paginação.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $studies = Study::with(['patient', 'serie.instance'])
            ->whereHas('serie.instance', function ($q) {
                if ($this->filtro !== 'todos') {
                    $q->where('status', $this->filtro);
                }
            })
            ->orderBy('study_date', 'DESC')
            ->paginate(5);
            
        return view('livewire.exames', ['studies' => $studies]);
    }
}
