<?php

namespace App\Http\Controllers;

use App\Models\Integracao;
use App\Http\Requests\StoreIntegracaoRequest;
use App\Http\Requests\UpdateIntegracaoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class IntegracaoController extends Controller
{
    public function index(): View
    {
        $integracoes = Integracao::withTrashed()->orderBy('sistema')->get();

        return view('integracoes.index', compact('integracoes'));
    }

    public function create(): View
    {
        return view('integracoes.create');
    }

    public function store(StoreIntegracaoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password_enc'] = !empty($data['password'])
            ? Crypt::encryptString($data['password'])
            : null;
        unset($data['password']);

        Integracao::create($data);

        return redirect()
            ->route('dev.integracoes.index')
            ->with('mensagem', 'Integração criada com sucesso.');
    }

    public function edit(Integracao $integracao): View
    {
        return view('integracoes.edit', compact('integracao'));
    }

    public function update(UpdateIntegracaoRequest $request, Integracao $integracao): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password_enc'] = Crypt::encryptString($data['password']);
        }
        unset($data['password']);

        $integracao->update($data);

        return redirect()
            ->route('dev.integracoes.index')
            ->with('mensagem', 'Integração atualizada com sucesso.');
    }

    public function destroy(Integracao $integracao): RedirectResponse
    {
        $integracao->delete();

        return redirect()
            ->route('dev.integracoes.index')
            ->with('mensagem', 'Integração inativada com sucesso.');
    }

    public function restore(int $id): RedirectResponse
    {
        $integracao = Integracao::withTrashed()->findOrFail($id);
        $integracao->restore();

        return redirect()
            ->route('dev.integracoes.index')
            ->with('mensagem', 'Integração ativada com sucesso.');
    }
}
