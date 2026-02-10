<x-guest-layout>
    <div class="mb-6">
        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-semibold text-foreground mb-2 text-center">Verifique seu email</h2>
        <p class="text-sm text-muted-foreground text-center">
            Obrigado por se cadastrar! Antes de começar, você poderia verificar seu endereço de email clicando no link que acabamos de enviar? Se você não recebeu o email, teremos prazer em enviar outro.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-4 bg-[#10b981]/10 border border-[#10b981]/20 rounded-lg">
            <p class="font-medium text-sm text-[#10b981] text-center">
                {{ __('Um novo link de verificação foi enviado para o endereço de email fornecido durante o registro.') }}
            </p>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full">
                {{ __('Reenviar email de verificação') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full text-sm text-muted-foreground hover:text-foreground font-medium transition-colors py-2"
            >
                {{ __('Sair') }}
            </button>
        </form>
    </div>
</x-guest-layout>
