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
    public array $openSeries  = [];

    public function setFiltro($filtro){
        $this->filtro = $filtro;
        $this->resetPage();
    }

    public function toggleStudy(int $studyId)
    {
        $this->openStudies[$studyId] =
            !($this->openStudies[$studyId] ?? false);
    }

    public function toggleSerie(int $serieId)
    {
        $this->openSeries[$serieId] =
            !($this->openSeries[$serieId] ?? false);
    }

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
