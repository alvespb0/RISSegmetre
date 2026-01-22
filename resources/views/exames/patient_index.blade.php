<x-guest-layout>
    <div class="w-full max-w-3xl mx-auto p-4 md:p-6">
        
        <div class="mb-8 text-center sm:text-left">
            <h2 class="text-3xl font-bold text-foreground tracking-tight">Meus Exames</h2>
            <p class="text-base text-muted-foreground mt-2">Gerencie e baixe os documentos do seu exame</p>
        </div>

        @if(session('mensagem'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium text-emerald-800">{{ session('mensagem') }}</p>
            </div>
        @endif

        <livewire:patient-exam :serie="$serie">

        <div class="mt-8 mb-4 text-center">
            <a 
                href="#" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Voltar para lista
            </a>
            <p class="text-xs text-muted-foreground mt-4">
                &copy; {{ date('Y') }} SEGMETRE. Todos os direitos reservados.
            </p>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>