<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PatientLoginRequest;
use App\Models\DeliveryProtocol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

class PatientLoginController extends Controller
{
    /**
     * Handle an incoming patient login request.
     *
     * @param  \App\Http\Requests\Auth\PatientLoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PatientLoginRequest $request): RedirectResponse
    {
        $protocol = DeliveryProtocol::where('protocolo', $request->protocolo)->first();

        if (!$protocol || !Hash::check($request->senha, $protocol->senha)) {
            \Log::warning('Tentativa de login de paciente falhou', [
                'protocolo' => $request->protocolo,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'protocolo' => 'Protocolo ou senha inválidos.',
            ]);
        }

        $now = now();
        $protocol->update([
            'visualizado' => true,
            'first_view_at' => $protocol->first_view_at ?? $now,
            'last_view_at' => $now,
        ]);

        \Log::info('Login de paciente realizado com sucesso', [
            'protocolo_id' => $protocol->id,
            'serie_id' => $protocol->laudo_id,
            'ip' => $request->ip(),
        ]);

        activity('logins')
                ->performedOn($protocol)
                ->causedBy(auth()->user()) 
                ->withProperties([
                    'ip' => request()->ip(),
                    'browser' => request()->userAgent(),
                    'plataforma' => request()->header('sec-ch-ua-platform')
                ])
                ->log('Fez login utilizando o protocolo de entrega.');

        session()->put('patient_protocol', $protocol->protocolo);
        
        return redirect()->route('patient.exames')
            ->with('mensagem', 'Acesso liberado! Bem-vindo.');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('patient_protocol');

        \Log::info('Logout de paciente realizado', [
            'ip' => request()->ip(),
        ]);

        return redirect()->route('login')
            ->with('mensagem', 'Você saiu com sucesso.');
    }

    public function loginViewByProtocol($protocol){
        $protocolo = DeliveryProtocol::where('protocolo', $protocol)->first();

        if(!$protocolo){
            return abort(404);
        }

        return view('auth/login-patient', ['protocol' => $protocolo]);
    }
}
