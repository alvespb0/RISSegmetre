<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\EmpresasSoc;
use App\Models\Study;
use App\Services\EmpresasSocService;

class EmpresasSocList extends Component
{
    public Study $study;
    public $empresas;
    public $search = '';

    public function mount(Study $study)
    {
        $this->study = $study;
    }

    public function getCodigoSeq($idEmpresa, EmpresasSocService $service){
        $return = $service->getCodSequencial($idEmpresa, $this->study);

        if($return['status'] !== true){
            $this->dispatch('toast-error', message: $return['message']);
            return;
        }

        $this->study->update([
            'codigo_sequencial_ficha' => $return['codSequencial']
        ]);

        /* if($study->status == 'laudado') */ # Disparar para salvar laudo soc

        $this->dispatch('toast-success', message: $return['message']);
    }

    public function render()
    {
        $query = EmpresasSoc::query();

        if($this->search){
            $query->where(function ($q) {
                $q->where('nome', 'like', '%' . $this->search . '%')
                    ->orWhere('cnpj', 'like', '%' . $this->search . '%')
                    ->orWhere('codigo_soc', 'like', '%' . $this->search . '%');
            });
        }

        $this->empresas = $query->orderBy('nome', 'asc')->get();

        return view('livewire.empresas-soc-list');
    }
}
