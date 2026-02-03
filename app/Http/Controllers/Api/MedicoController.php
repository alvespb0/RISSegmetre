<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\MedicoIndexRequest;

use App\Models\MedicoLaudo;
use App\Models\ApiToken;

class MedicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MedicoIndexRequest $request)
    {
        $empresa = $this->getEmpresa($request->header('Authorization'));

        $medicos = MedicoLaudo::where('empresas_laudo_id', $empresa->id);

        if ($request->has('ativo')) {
            if ($request->boolean('ativo') === false) {
                $medicos->onlyTrashed();
            }
        }

        if(!empty($request->nome)){
            $medicos->where('nome', 'LIKE', '%'.$request->nome.'%');
        }

        if(!empty($request->conselho_classe)){
            $medicos->where('conselho_classe', 'LIKE', '%'.$request->conselho_classe.'%');
        }

        if(!empty($request->especialidade)){
            $medicos->where('especialidade', 'LIKE', '%'.$request->especialidade.'%');
        }

        return $medicos->get()->makeHidden(['empresas_laudo_id']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'especialidade' => 'required|string|max:255',
            'conselho_classe' => 'required|string|max:255'
        ]);

        $empresa = $this->getEmpresa($request->header('Authorization'));

        $medico = MedicoLaudo::create([
            'empresas_laudo_id' => $empresa->id,
            'nome' => $validated['nome'],
            'especialidade' => $validated['especialidade'],
            'conselho_classe' => $validated['conselho_classe']
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Médico cadastrado com sucesso',
            'medico' => $medico->makeHidden(['empresas_laudo_id'])
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $empresa = $this->getEmpresa($request->header('Authorization'));

        if ($empresa->medico->isEmpty()) {
            return response()->json([
                'status' => false,
                'error' => 'Empresa não possui médico cadastrado.'
            ], 422);
        }

        if (!$empresa->medico->contains('id', $id)) {
            return response()->json([
                'status' => false,
                'error' => 'Médico informado não pertence à empresa vinculada ao token'
            ], 404);
        }

        return MedicoLaudo::findOrFail($id)->makeHidden('empresas_laudo_id');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nome' => 'nullable|string|max:255',
            'especialidade' => 'nullable|string|max:255',
            'conselho_classe' => 'nullable|string|max:255'
        ]);

        $empresa = $this->getEmpresa($request->header('Authorization'));

        $medico = MedicoLaudo::where('id', $id)
            ->where('empresas_laudo_id', $empresa->id)
            ->first();

        if (!$medico) {
            return response()->json([
                'status' => false,
                'error' => 'Médico não encontrado ou não pertence à empresa.'
            ], 404);
        }

        $medico->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Médico atualizado com sucesso',
            'medico' => $medico->makeHidden(['empresas_laudo_id'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $empresa = $this->getEmpresa($request->header('Authorization'));
        $medico = MedicoLaudo::where('id', $id)
            ->where('empresas_laudo_id', $empresa->id)
            ->first();

        if (!$medico) {
            return response()->json([
                'status' => false,
                'error' => 'Médico não encontrado ou não pertence à empresa.'
            ], 404);
        }

        $medico->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medico inativado com sucesso'
        ], 200);
    }

    private function getEmpresa($authHeader){
        $token = substr($authHeader, 7);
        
        $hashedToken = hash('sha256', $token);

        $tokenModel = ApiToken::where('token', $hashedToken)->first();
        
        $empresa = $tokenModel->empresa;

        return $empresa;
    }
}
