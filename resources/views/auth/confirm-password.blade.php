<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-foreground mb-2">Confirmar senha</h2>
        <p class="text-sm text-muted-foreground">
            Esta é uma área segura da aplicação. Por favor, confirme sua senha antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

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

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Confirmar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
