<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicoLaudo;
use App\Models\ApiToken;

class MedicoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $empresa = $this->getEmpresa($request->header('Authorization'));
        $medicos = MedicoLaudo::where('empresas_laudo_id', $empresa->id)->get();
        return $medicos;
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

        MedicoLaudo::create([
            'empresas_laudo_id' => $empresa->id,
            'nome' => $validated['nome'],
            'especialidade' => $validated['especialidade'],
            'conselho_classe' => $validated['conselho_classe']
        ]);

        return response()->json([
            'Médico cadastrado com sucesso'
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
            ], 422);
        }

        return MedicoLaudo::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getEmpresa($authHeader){
        $token = substr($authHeader, 7);
        
        $hashedToken = hash('sha256', $token);

        $tokenModel = ApiToken::where('token', $hashedToken)->first();
        
        $empresa = $tokenModel->empresa;

        return $empresa;
    }
}
