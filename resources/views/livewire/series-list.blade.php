<div x-data="{ 
    modalAnamneseOpen: false,
    selectedInstanceId: null,
    anamneseTexto: '',
    isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},
    
    abrirAnamnese(instanceId, anamnese) {
        // Atualiza variáveis visuais do Alpine
        this.selectedInstanceId = instanceId;
        this.anamneseTexto = anamnese || '';
        this.modalAnamneseOpen = true;

        // Atualiza diretamente as propriedades do componente Livewire
        // Isso substitui a necessidade de inputs hidden e setTimeout
        $wire.set('instanceId', instanceId);
        $wire.set('anamnese', anamnese || '');
    }
}">
    @if(($filtro === 'todos' ? $serie->instance : $serie->instance->filter(fn($i) => $i->status === $filtro))->count() > 0)
        <div class="relative">
            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-primary/20"></div>
            
            <div class="ml-6 pl-4 pr-4 py-3 bg-card border border-border rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:border-primary/30">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-md bg-primary/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Modalidade</p>
                                    <p class="font-semibold text-foreground">{{ $serie->modality }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-4">
                            <p class="text-xs text-muted-foreground">Parte do Corpo</p>
                            <p class="text-sm text-foreground">{{ $serie->body_part_examined ?? '—' }}</p>
                        </div>

                        <div class="col-span-3">
                            <p class="text-xs text-muted-foreground mb-1">Total de Exames</p>
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-primary/10 text-primary rounded-md">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ ($filtro === 'todos' ? $serie->instance : $serie->instance->filter(fn($i) => $i->status === $filtro))->count() }}
                            </span>
                        </div>

                        <div class="col-span-2 text-right">
                            <button
                                wire:click="toggleSerie({{ $serie->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/10 rounded-md transition-colors"
                            >
                                <svg class="w-3.5 h-3.5 transition-transform {{ ($openSeries[$serie->id] ?? false) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                {{ ($openSeries[$serie->id] ?? false) ? 'Ocultar' : 'Ver exames' }}
                            </button>
                        </div>
                    </div>
                </div>

                @if($openSeries[$serie->id] ?? false)
                    <div class="mt-3 ml-12 pl-4 border-l-2 border-primary/20 space-y-2">
                        @forelse(($filtro === 'todos' ? $serie->instance : $serie->instance->filter(fn($i) => $i->status === $filtro)) as $instance)
                            <livewire:InstancesList :instance="$instance" :filtro="$filtro" :wire:key="$instance->id"/>
                        @empty
                            <div class="ml-4 pl-3 py-2.5 text-xs text-muted-foreground italic bg-muted/20 border border-border/30 rounded-md">
                                Nenhuma instância encontrada
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    @endif
    
</div>