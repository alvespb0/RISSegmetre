<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-foreground mb-2">Bem-vindo de volta</h2>
        <p class="text-sm text-muted-foreground">Entre com suas credenciais para acessar o sistema</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
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
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded border-border text-primary focus:ring-primary focus:ring-offset-0" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-foreground">{{ __('Lembrar-me') }}</span>
            </label>

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

    @if (Route::has('register'))
        <div class="mt-6 text-center">
            <p class="text-sm text-muted-foreground">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="text-primary hover:text-primary/80 font-medium transition-colors">
                    {{ __('Criar conta') }}
                </a>
            </p>
        </div>
    @endif
</x-guest-layout>
