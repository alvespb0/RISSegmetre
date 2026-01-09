<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Serie;
use App\Models\Instance;

class SeriesList extends Component
{

    public $serie;
    public $filtro;
    public array $openSeries  = [];
    public $anamnese;
    public $instanceId;
    public $liberarTecnico = [];

    public function mount(Serie $serie, $filtro){
        $this->serie = $serie;
        $this->filtro = $filtro;
    }

    public function toggleSerie(int $serieId)
    {
        $this->openSeries[$serieId] =
            !($this->openSeries[$serieId] ?? false);
    }

    public function render()
    {
        return view('livewire.series-list');
    }
}
