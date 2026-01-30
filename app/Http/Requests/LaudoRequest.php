<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaudoRequest extends FormRequest
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
            'status'       => 'required|in:laudado',
            'laudo_texto'  => 'required|string|min:10',
            'laudo_pdf' => [
                'required',
                'string',
                function ($attr, $value, $fail) {
                    $bin = base64_decode($value, true);

                    if ($bin === false) {
                        $fail('PDF inválido (base64).');
                        return;
                    }

                    if (substr($bin, 0, 4) !== '%PDF') {
                        $fail('Arquivo enviado não é um PDF.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required'      => 'O status é obrigatório.',
            'status.in'            => 'Status inválido.',

            'laudo_texto.required' => 'O texto do laudo é obrigatório.',
            'laudo_texto.min'      => 'O texto do laudo deve ter pelo menos :min caracteres.',

            'laudo_pdf.required'   => 'O PDF do laudo é obrigatório.',
        ];
    }

}
