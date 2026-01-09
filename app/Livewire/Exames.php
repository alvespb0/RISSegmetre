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


    public $anamnese;
    public $instanceId;
    public $liberarTecnico = [];
    
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

    public function setAnamnese(){
        try {
            $instance = Instance::findOrFail($this->instanceId);

            $instance->update([
                'anamnese' => $this->anamnese
            ]);

            $this->liberadoTec[$instanceId] = true;

            $this->dispatch('toast-success', message: 'Anamnese atualizada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar anamnese da instancia com o id: '. $this->instanceId . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao atualizar anamnese: ' . $e->getMessage());
        }
    }
    
    public function liberarExame($instanceId){
        try{
            $instance = Instance::findOrFail($instanceId);

            $instance->update([
                'liberado_tec' => true
            ]);

            $this->dispatch('toast-success', message: 'Exame liberado para Dr (a) com sucesso!');
        }catch(\Excpetion $e){
            \Log::error('Erro ao liberar instância para Dra, id da instância: ' . $instanceId . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao liberar exame para Dra: ' . $e->getMessage());
        }
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
