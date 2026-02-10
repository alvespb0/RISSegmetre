<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiToken;
use App\Models\EmpresaLaudo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function show(){
        $tokens = ApiToken::with('empresa')->get();

        return view('dev/api-tokens', ['tokens' => $tokens]);
    }

    public function store(Request $request){
        $plainToken = Str::random(60);
        
        $apiToken = ApiToken::create([
            'name' => $request->name,
            'token' => hash('sha256', $plainToken),
            'active' => true,
        ]);

        // Criar empresa associada
        EmpresaLaudo::create([
            'token_id' => $apiToken->id,
            'nome' => $request->name,
            'cnpj' => $request->cnpj,
        ]);

        // Retornar token limpo (sem hash) apenas uma vez
        return response()->json([
            'success' => true,
            'token' => $plainToken,
            'message' => 'Token gerado com sucesso!'
        ]);
    }

    public function toggleActive($id){
        $token = ApiToken::findOrFail($id);
        $token->active = !$token->active;
        $token->save();

        return response()->json([
            'success' => true,
            'active' => $token->active,
            'message' => $token->active ? 'Token ativado com sucesso!' : 'Token inativado com sucesso!'
        ]);
    }
}
