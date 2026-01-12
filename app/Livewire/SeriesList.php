<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

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
    public $laudo = '';

    public function mount(Serie $serie, $filtro){
        $this->serie = $serie;
        $this->filtro = $filtro;
    }

    public function setLaudo(){
        try{
            $this->serie->update([
                'laudo' => $this->laudo,
                'medico_id' => Auth::id()
            ]);
            
            $this->dispatch('toast-success', message: 'Laudo realizado com sucesso!');
            $this->dispatch('close-modal-laudo-' . $this->serie->id);
        }catch (\Exception $e) {
            Log::error('Erro ao laudar Série: ' . $this->serie->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao laudar Série: ' . $e->getMessage());
        }
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
