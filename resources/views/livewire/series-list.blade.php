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
                            @if((Auth::user()->tipo == 'medico' && $instance->liberado_tec == true) || Auth::user()->tipo != 'medico')
                                <div class="ml-4 pl-3 pr-3 py-2.5 bg-muted/30 border border-border/50 rounded-md hover:bg-muted/50 hover:border-primary/30 transition-all duration-150">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-6 h-6 rounded bg-primary/15 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-muted-foreground">ID do Exame</p>
                                                <p class="text-sm font-medium text-foreground truncate">
                                                    {{ $instance->instance_external_id ?? 'Exame #' . $instance->id }}
                                                </p>
                                                
                                                @if(Auth::user()->tipo !== 'medico')
                                                    <button 
                                                        type="button"
                                                        @click="abrirAnamnese({{ $instance->id }}, atob('{{ base64_encode($instance->anamnese ?? '') }}'))"
                                                        class="mt-1 text-xs text-primary hover:underline font-medium"
                                                    >
                                                        {{ $instance->anamnese ? 'Visualizar/Editar Anamnese' : 'Escrever Anamnese' }}
                                                    </button>
                                                @else
                                                    @if($instance->anamnese)
                                                        <button 
                                                            type="button"
                                                            @click="abrirAnamnese({{ $instance->id }}, atob('{{ base64_encode($instance->anamnese) }}'))"
                                                            class="mt-1 text-xs text-primary hover:underline font-medium"
                                                        >
                                                            Visualizar Anamnese
                                                        </button>
                                                    @else
                                                        <p class="text-xs text-muted-foreground mt-0.5 italic">Nenhuma anamnese registrada</p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                                @if($instance->status === 'laudado') bg-green-500/10 text-green-600 dark:text-green-400
                                                @elseif($instance->status === 'rejeitado') bg-red-500/10 text-red-600 dark:text-red-400
                                                @else bg-yellow-500/10 text-yellow-600 dark:text-yellow-400
                                                @endif">
                                                {{ ucfirst($instance->status ?? 'pendente') }}
                                            </span>
                                            @if(Auth::user()->tipo !== 'medico')
                                                <button
                                                    wire:click="liberarExame({{ $instance->id }})"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-colors
                                                        {{ ($liberadoTec[$instance->id] ?? $instance->liberado_tec)
                                                            ? 'bg-primary/10 text-primary border border-primary/20'
                                                            : 'bg-muted text-muted-foreground border border-border hover:bg-accent' }}"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ ($liberadoTec[$instance->id] ?? $instance->liberado_tec) ? 'Liberado' : 'Liberar' }}
                                                </button>
                                            @endif
                                            <button class="p-1.5 text-primary hover:bg-primary/10 rounded-md transition-colors" title="Visualizar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="ml-4 pl-3 pr-3 py-2.5 bg-muted/30 border border-border/50 rounded-md hover:bg-muted/50 hover:border-primary/30 transition-all duration-150">
                                    <p>Exame aguardando liberação do técnico.</p>
                                </div>
                            @endif
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
    
    <div 
        x-show="modalAnamneseOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click="modalAnamneseOpen = false; anamneseTexto = ''; selectedInstanceId = null"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        ></div>

        <div 
            class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-2xl mx-4 p-6"
            @click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-foreground">
                    <span x-text="isMedico ? 'Visualizar Anamnese' : 'Escrever/Visualizar Anamnese'"></span>
                </h3>
                <button
                    @click="modalAnamneseOpen = false; anamneseTexto = ''; selectedInstanceId = null"
                    class="p-1 hover:bg-accent rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="setAnamnese">
                <div class="mb-6">
                    <label for="anamnese" class="block text-sm font-medium text-foreground mb-2">
                        Anamnese <span x-show="!isMedico" class="text-destructive">*</span>
                    </label>
                    
                    <div x-show="isMedico" class="w-full px-4 py-3 bg-input-background border border-border rounded-lg min-h-[200px] max-h-[400px] overflow-y-auto">
                        <p class="text-foreground whitespace-pre-wrap" x-text="anamneseTexto || 'Nenhuma anamnese registrada'"></p>
                    </div>

                    <textarea
                        x-show="!isMedico"
                        id="anamnese"
                        wire:model="anamnese"
                        placeholder="Descreva a anamnese do paciente (sintomas, histórico, queixas principais, etc.)"
                        class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[200px] resize-y"
                        rows="8"
                    ></textarea>
                    
                    <p class="text-xs text-muted-foreground mt-2">
                        <span x-show="!isMedico">Esta anamnese será registrada no sistema e associada ao exame.</span>
                        <span x-show="isMedico">Visualização apenas. Você não pode editar a anamnese.</span>
                    </p>
                </div>

                <div class="flex gap-3 justify-end">
                    <button
                        type="button"
                        @click="modalAnamneseOpen = false; anamneseTexto = ''; selectedInstanceId = null"
                        class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors"
                    >
                        Fechar
                    </button>
                    <button
                        x-show="!isMedico"
                        type="submit"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                    >
                        Salvar Anamnese
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>