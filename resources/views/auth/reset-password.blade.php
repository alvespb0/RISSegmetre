<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-foreground mb-2">Redefinir senha</h2>
        <p class="text-sm text-muted-foreground">
            Digite sua nova senha abaixo para concluir a redefinição.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                id="email" 
                type="email" 
                name="email" 
                :value="old('email', $request->email)" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="seu@email.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Nova senha')" />
            <x-text-input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar nova senha')" />
            <x-text-input 
                id="password_confirmation"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="••••••••"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Redefinir senha') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-primary hover:text-primary/80 font-medium transition-colors">
            ← {{ __('Voltar para login') }}
        </a>
    </div>
</x-guest-layout>
