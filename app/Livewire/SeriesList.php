<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;

use Livewire\Component;
use App\Models\Serie;
use App\Models\Instance;

use App\Services\LaudoService;
use App\Services\ProtocoloService;

class SeriesList extends Component
{

    public $serie;
    public $filtro;
    public array $openSeries  = [];
    public $anamnese;
    public $instanceId;
    public $liberarTecnico = [];
    public $laudo = '';
    public $rejeicao = '';

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
            
            $service = new LaudoService;

            $file = $service->gerarLaudo($this->serie, $this->laudo);

            $this->serie->update([
                'laudo_path' => $file['pdf'],
            ]);

            $this->serie->instance()->update([
                'status' => 'laudado'
            ]);

            $this->dispatch('toast-success', message: 'Laudo gerado com sucesso!');
            $this->dispatch('close-modal-laudo-' . $this->serie->id);
        }catch (\Exception $e) {
            \Log::error('Erro ao laudar Série: ' . $this->serie->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao laudar Série: ' . $e->getMessage());
        }
    }
    
    public function gerarProtocolo(){
        try{
            $service = new ProtocoloService();

            $service->gerarProtocolo($this->serie);
            $this->dispatch('toast-success', message: 'Protocolo de entrega gerado com sucesso!');
        }catch (\Exception $e) {
            \Log::error('Erro ao gerar protocolo de entrega da série: ' . $this->serie->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao gerar protocolo de entrega da Série: ' . $e->getMessage());
        }
    }

    public function setRejeicao(){
        try{
            $this->serie->update([
                'medico_id' => Auth::id(),
                'motivo_rejeicao' => $this->rejeicao
            ]);
            
            $this->serie->instance()->update([
                'status' => 'rejeitado'
            ]);

            $this->dispatch('toast-success', message: 'Exame rejeitado!');
            $this->dispatch('close-modal-rejeicao-' . $this->serie->id);
        }catch (\Exception $e) {
            \Log::error('Erro ao rejeitar série: ' . $this->serie->id . ', erro: '. $e->getMessage());
            $this->dispatch('toast-error', message: 'Erro ao rejeitar exame: ' . $e->getMessage());
        }
    }

    public function baixarLaudo(){
        redirect()->route('baixar.laudo', $this->serie->id);
    }

    public function baixarProtocolo(){
        redirect()->route('baixar.protocolo', $this->serie->id);
    }

    public function toggleSerie(int $serieId){
        $this->openSeries[$serieId] =
            !($this->openSeries[$serieId] ?? false);
    }

    public function render()
    {
        return view('livewire.series-list');
    }
}
