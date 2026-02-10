<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicoIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ativo' => 'nullable|boolean',
            'nome' => 'nullable|string|min:1|max:255',
            'conselho_classe' => 'nullable|string|min:1|max:255',
            'especialidade' => 'nullable|string|min:1|max:255'
        ];
    }


    public function messages(): array
    {
        return [
            'ativo.boolean' => 'O campo status deve ser 0 ou 1.',

            'nome.string' => 'O campo nome deve ser um texto válido.',
            'nome.min'    => 'O campo nome deve conter ao menos 1 caractere.',
            'nome.max'    => 'O campo nome não pode ter mais que 255 caracteres.',

            'conselho_classe.string' => 'O campo conselho de classe deve ser um texto válido.',
            'conselho_classe.min'    => 'O campo conselho de classe deve conter ao menos 1 caractere.',
            'conselho_classe.max'    => 'O campo conselho de classe não pode ter mais que 255 caracteres.',

            'especialidade.string' => 'O campo especialidade deve ser um texto válido.',
            'especialidade.min'    => 'O campo especialidade deve conter ao menos 1 caractere.',
            'especialidade.max'    => 'O campo especialidade não pode ter mais que 255 caracteres.',
        ];
    }

}
