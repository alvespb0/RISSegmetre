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

        <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden" x-data="{ showImages: false }">
            
            <div class="p-6 md:p-8 border-b border-border/60">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-primary/10 rounded-xl text-primary shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Data do Exame</p>
                            <p class="text-xl font-semibold text-foreground mt-1">
                                {{ $serie->study->study_date ?? '--/--/----' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-blue-500/10 rounded-xl text-blue-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Parte Examinada</p>
                            <p class="text-xl font-semibold text-foreground mt-1">
                                {{ $serie->body_part_examined ?? 'Não especificado' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8 bg-muted/30">
                <h3 class="text-base font-semibold text-foreground mb-4 flex items-center gap-2">
                    Arquivos Disponíveis
                </h3>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="button" class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md active:scale-[0.99] font-semibold text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Baixar Laudo PDF
                    </button>

                    <button 
                        type="button" 
                        @click="showImages = !showImages"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-white dark:bg-zinc-800 border border-border text-foreground rounded-lg hover:bg-accent hover:text-accent-foreground transition-all shadow-sm hover:shadow-md font-semibold text-base"
                        :class="{ 'ring-2 ring-primary/20 border-primary': showImages }"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="showImages ? 'Ocultar Imagens' : 'Ver Imagens Disponíveis'"></span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': showImages }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

                <div 
                    x-show="showImages" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="mt-4 bg-background border border-border rounded-lg divide-y divide-border shadow-sm"
                >
                    @forelse($serie->instance as $instance)
                        <div class="flex items-center justify-between p-4 hover:bg-accent/50 transition-colors group">
                            <div class="flex items-center gap-4 overflow-hidden">
                                <div class="bg-muted p-2.5 rounded-lg text-muted-foreground group-hover:text-primary transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-sm font-semibold text-foreground truncate">Imagem {{ $loop->iteration }}</span>
                                    <span class="text-xs text-muted-foreground truncate font-mono mt-0.5">ID: {{ $instance->instance_external_id }}</span>
                                </div>
                            </div>
                            
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-primary bg-primary/10 hover:bg-primary/20 rounded-md transition-colors whitespace-nowrap">
                                Baixar
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-3">
                                <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-foreground">Nenhuma imagem disponível</p>
                            <p class="text-xs text-muted-foreground mt-1">Não encontramos arquivos de imagem para este exame.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
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