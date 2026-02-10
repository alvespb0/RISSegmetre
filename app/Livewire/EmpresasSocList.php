<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\EmpresasSoc;
use App\Models\Study;
use App\Services\EmpresasSocService;

class EmpresasSocList extends Component
{
    public $study;
    public $empresas;
    public $search = '';

    public function mount(Study $study)
    {
        $this->study = $study;
    }

    public function getCodigoSeq($idEmpresa){
        $service = new EmpresasSocService;
    
        $return = $service->getCodSequencial($idEmpresa, $this->study);

        if($return['status'] !== true){
            $this->dispatch('toast-error', message: $return['message']);
            return;
        }

        $this->study->update([
            'cod_sequencial_ficha' => $return['codSequencial']
        ]);

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
