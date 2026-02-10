<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PatientLoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'protocolo' => ['required', 'string', 'max:255'],
            'senha' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'protocolo.required' => 'O protocolo é obrigatório.',
            'protocolo.string' => 'O protocolo deve ser um texto válido.',
            'protocolo.max' => 'O protocolo não pode ter mais de 255 caracteres.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.string' => 'A senha deve ser um texto válido.',
        ];
    }
}
