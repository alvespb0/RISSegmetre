<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIntegracaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $integracaoId = $this->route('integracao');

        return [
            'sistema'    => 'required|string|max:255',
            'descricao'  => 'nullable|string|max:500',
            'endpoint'   => 'required|string|max:500',
            'slug'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('integracoes', 'slug')->ignore($integracaoId),
            ],
            'username'   => 'nullable|string|max:255',
            'password'   => 'nullable|string|max:255',
            'auth'       => 'required|in:basic,bearer,wss',
            'tipo'       => 'required|in:soap,rest',
        ];
    }

    public function messages(): array
    {
        return [
            'sistema.required'   => 'O sistema é obrigatório.',
            'sistema.max'        => 'O sistema não pode ter mais de :max caracteres.',
            'endpoint.required'  => 'O endpoint é obrigatório.',
            'slug.required'      => 'O slug é obrigatório.',
            'slug.unique'        => 'Este slug já está em uso.',
            'auth.required'      => 'O tipo de autenticação é obrigatório.',
            'auth.in'            => 'Tipo de autenticação inválido.',
            'tipo.required'      => 'O tipo de integração é obrigatório.',
            'tipo.in'            => 'Tipo de integração inválido.',
        ];
    }
}
