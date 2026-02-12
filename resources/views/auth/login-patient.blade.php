<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-foreground mb-2">Bem-vindo de volta</h2>
        <p class="text-sm text-muted-foreground">Entre com suas credenciais para acessar o sistema</p>
    </div>
    <!-- Formulário de Paciente -->
        <form method="POST" action="{{ route('patient.login') }}" class="space-y-5">
            @csrf
            <!-- Protocolo -->
            <div>
                <x-input-label for="protocolo" :value="__('Protocolo')" />
                <x-text-input 
                    id="protocolo" 
                    type="text" 
                    name="protocolo" 
                    value="{{$protocol->protocolo}}"
                    required
                    autofocus 
                    autocomplete="username"
                    placeholder="Digite seu protocolo"
                />
                <x-input-error :messages="$errors->get('protocolo')" class="mt-2" />
            </div>
            <!-- Senha -->
            <div>
                <x-input-label for="patient_password" :value="__('Senha')" />
                <x-text-input 
                    id="patient_password"
                    type="password"
                    name="senha"
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('senha')" class="mt-2" />
            </div>
            <div class="pt-2">
                <x-primary-button class="w-full">
                    {{ __('Acessar Meus Exames') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>
