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

    /**
     * Inicializa o componente com a Série selecionada e o filtro de status atual.
     *
     * @param \App\Models\Serie $serie Série carregada via route-model binding.
     * @param mixed $filtro Filtro de status (ex.: 'pendente', 'laudado', 'rejeitado', 'todos').
     * @return void
     */
    public function mount(Serie $serie, $filtro){
        $this->serie = $serie;
        $this->filtro = $filtro;
    }

    /**
     * Persiste o laudo da série, gera o PDF e atualiza o status das instâncias relacionadas.
     *
     * Dispara eventos de toast e fechamento de modal.
     *
     * @return void
     */
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
    
    /**
     * Gera o protocolo de entrega da série.
     *
     * Dispara eventos de toast em caso de sucesso/erro.
     *
     * @return void
     */
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

    /**
     * Registra o motivo de rejeição e atualiza o status das instâncias relacionadas para "rejeitado".
     *
     * Dispara eventos de toast e fechamento de modal.
     *
     * @return void
     */
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

    /**
     * Redireciona para a rota de download do laudo da série.
     *
     * @return void
     */
    public function baixarLaudo(){
        redirect()->route('baixar.laudo', $this->serie->id);
    }

    /**
     * Redireciona para a rota de download do protocolo de entrega da série.
     *
     * @return void
     */
    public function baixarProtocolo(){
        redirect()->route('baixar.protocolo', $this->serie->id);
    }

    /**
     * Alterna (abre/fecha) o estado de expansão de uma Série na lista.
     *
     * @param int $serieId ID da Série.
     * @return void
     */
    public function toggleSerie(int $serieId){
        $this->openSeries[$serieId] =
            !($this->openSeries[$serieId] ?? false);
    }

    /**
     * Renderiza a view do componente.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.series-list');
    }
}
