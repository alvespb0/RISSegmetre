<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-foreground mb-2">Bem-vindo de volta</h2>
        <p class="text-sm text-muted-foreground">Entre com suas credenciais para acessar o sistema</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{ activeTab: 'profissional' }">
        <!-- Tabs para alternar entre os formulários -->
        <div class="mb-6">
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    @click="activeTab = 'profissional'"
                    :class="activeTab === 'profissional' ? 'bg-primary text-primary-foreground border-primary' : 'bg-card text-foreground border-border hover:bg-accent'"
                    class="px-4 py-3 rounded-lg border-2 transition-all duration-200 flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">Profissional</span>
                </button>
                <button
                    type="button"
                    @click="activeTab = 'paciente'"
                    :class="activeTab === 'paciente' ? 'bg-primary text-primary-foreground border-primary' : 'bg-card text-foreground border-border hover:bg-accent'"
                    class="px-4 py-3 rounded-lg border-2 transition-all duration-200 flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="font-medium">Paciente</span>
                </button>
            </div>
        </div>

        <!-- Formulário de Profissional -->
        <div x-show="activeTab === 'profissional'" x-cloak>
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input 
                        id="email" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required
                        autofocus 
                        autocomplete="username"
                        placeholder="seu@email.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Senha')" />
                    <x-text-input 
                        id="password"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <a 
                            class="text-sm text-primary hover:text-primary/80 font-medium transition-colors" 
                            href="{{ route('password.request') }}"
                        >
                            {{ __('Esqueceu a senha?') }}
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <x-primary-button class="w-full">
                        {{ __('Entrar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Formulário de Paciente -->
        <div x-show="activeTab === 'paciente'" x-cloak style="display: none;">
            <form method="POST" action="{{ route('patient.login') }}" class="space-y-5">
                @csrf
                <!-- Protocolo -->
                <div>
                    <x-input-label for="protocolo" :value="__('Protocolo')" />
                    <x-text-input 
                        id="protocolo" 
                        type="text" 
                        name="protocolo" 
                        :value="old('protocolo')" 
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
    </div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>
