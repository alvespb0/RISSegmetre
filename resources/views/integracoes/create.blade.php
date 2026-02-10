<x-app-layout>
    <div class="p-6 space-y-6 max-w-2xl mx-auto">
        <div>
            <a href="{{ route('dev.integracoes.index') }}" class="text-sm text-muted-foreground hover:text-foreground mb-4 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Voltar
            </a>
            <h2 class="text-2xl font-semibold text-foreground mt-2">Nova Integração</h2>
            <p class="text-muted-foreground">Cadastre uma nova integração de sistema externo</p>
        </div>

        <div class="bg-card border border-border rounded-lg p-6 max-w-2xl mx-auto">
            <form action="{{ route('dev.integracoes.store') }}" method="POST" class="space-y-4">
                @csrf
                @include('integracoes._form', ['integracao' => null])
                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium"
                    >
                        Criar Integração
                    </button>
                    <a
                        href="{{ route('dev.integracoes.index') }}"
                        class="px-4 py-2 border border-border rounded-lg hover:bg-sidebar-accent/50 transition-colors font-medium"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
