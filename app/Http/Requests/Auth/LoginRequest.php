<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
        $rules = [
            'password' => ['required', 'string'],
        ];

        // Se for paciente, valida protocolo, senão valida email
        $isPaciente = $this->input('is_paciente') === '1' || $this->boolean('is_paciente');
        
        if ($isPaciente) {
            $rules['protocolo'] = ['required', 'string'];
        } else {
            $rules['email'] = ['required', 'string', 'email'];
        }

        return $rules;
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $isPaciente = $this->input('is_paciente') === '1' || $this->boolean('is_paciente');
        
        if ($isPaciente) {
            // Buscar por protocolo (que pode ser o email ou um campo específico)
            // Assumindo que protocolo pode ser usado como identificador único
            $user = \App\Models\User::where('tipo', 'paciente')
                ->where(function($query) {
                    $query->where('email', $this->string('protocolo'))
                          ->orWhere('id', $this->string('protocolo'));
                })
                ->first();

            if (!$user || !\Illuminate\Support\Facades\Hash::check($this->string('password'), $user->password)) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'protocolo' => trans('auth.failed'),
                ]);
            }

            Auth::login($user, $this->boolean('remember'));
        } else {
            if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $isPaciente = $this->input('is_paciente') === '1' || $this->boolean('is_paciente');
        $identifier = $isPaciente 
            ? $this->string('protocolo') 
            : $this->string('email');
        
        return Str::transliterate(Str::lower($identifier).'|'.$this->ip());
    }
}
