<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Instance;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Log;

class InstancesList extends Component
{
    public Instance $instance; 
    public $filtro;
    public $anamnese = ''; 
    public $liberadoTec = false;

    public function mount(Instance $instance, $filtro)
    {
        $this->instance = $instance;
        $this->filtro = $filtro;
        $this->anamnese = $instance->anamnese;
        $this->liberadoTec = $instance->liberado_tec;
    }

    public function setAnamnese()
    {
        try {
            $this->instance->update([
                'anamnese' => $this->anamnese
            ]);

            $this->dispatch('toast-success', message: 'Anamnese atualizada com sucesso!');
            $this->dispatch('close-modal-anamnese-' . $this->instance->id);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar anamnese ID: '. $this->instance->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao atualizar: ' . $e->getMessage());
        }
    }
    
    public function liberarExame()
    {
        try {
            $this->instance->update([
                'liberado_tec' => true
            ]);

            $this->liberadoTec = true; 
            $this->dispatch('toast-success', message: 'Exame liberado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao liberar instância: ' . $this->instance->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao liberar: ' . $e->getMessage());
        }
    }

    public function downloadDCM(){
        $url = route('baixar.dicom', ['id' => $this->instance->instance_external_id]);

        return redirect()->to($url);
    }

    public function render()
    {
        return view('livewire.instances-list');
    }
}