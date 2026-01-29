<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Instance;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class InstancesList extends Component
{
    public $instance; 
    public $filtro;
    public $anamnese = ''; 
    public $liberadoTec = false;

    /**
     * Inicializa o componente com a instância selecionada e o filtro atual.
     *
     * @param \App\Models\Instance $instance Instância carregada via route-model binding.
     * @param mixed $filtro Filtro de status (ex.: 'pendente', 'laudado', 'rejeitado', 'todos').
     * @return void
     */
    public function mount(Instance $instance, $filtro)
    {
        $this->instance = $instance;
        $this->filtro = $filtro;
        $this->anamnese = $instance->anamnese;
        $this->liberadoTec = $instance->liberado_tec;
    }

    /**
     * Persiste a anamnese da instância.
     *
     * Dispara eventos de toast e fechamento de modal.
     *
     * @return void
     */
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
    
    /**
     * Libera a instância para o técnico (marca liberado_tec = true).
     *
     * Dispara eventos de toast em caso de sucesso/erro.
     *
     * @return void
     */
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

    /**
     * Redireciona para a rota de download do DICOM (DCM) da instância.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadDCM(){
        $idEnc = Crypt::encryptString($this->instance->instance_external_id);

        activity('downloads')
                ->performedOn($this->instance)
                ->causedBy(auth()->user()) 
                ->withProperties([
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                    'plataforma' => request()->header('sec-ch-ua-platform')
                ])
                ->log('Fez o download das imagens.');

        return redirect()->route('baixar.dicom', $idEnc);
    }

    /**
     * Renderiza a view do componente.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.instances-list');
    }
}