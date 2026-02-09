<div x-data="{ 
    modalLaudoOpen: false,
    modalRejeicaoOpen: false,
    isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},
    init() {
        Livewire.on('close-modal-laudo-{{ $serie->id }}', () => {
            this.modalLaudoOpen = false;
        });
        Livewire.on('close-modal-rejeicao-{{ $serie->id }}', () => {
            tihs.modalRejeicaoOpen = false;
        }); 
    }

}">
    <div class="relative">
        <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-primary/20"></div>
        
        <div class="ml-6 pl-4 pr-4 py-3 bg-card border border-border rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:border-primary/30">
            <div class="flex items-center justify-between gap-4">
                <div class="flex-1 grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-2">
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
                    <div class="col-span-2">
                        <p class="text-xs text-muted-foreground mb-1">Total de Exames</p>
                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-primary/10 text-primary rounded-md">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ $serie->instance->count() }}
                        </span>
                    </div>
                    <div class="col-span-4 flex items-center justify-end gap-1.5 mt-2">
                            {{-- Botão Laudo --}}
                            @if($serie->study->status != 'rejeitado')
                                <button 
                                    type="button"
                                    class="whitespace-nowrap px-2.5 py-1 text-[11px] font-medium bg-primary/5 text-primary hover:bg-primary/15 rounded-md border border-primary/10 transition-all"
                                    @click="modalLaudoOpen = true"
                                >
                                    @if(Auth::user()->tipo == 'medico')
                                        {{ $serie->laudo()->exists() ? 'Editar Laudo' : 'Digitar Laudo'}}
                                    @else
                                        {{ $serie->laudo()->exists() ? 'Ver Laudo' : 'Sem Laudo'}}
                                    @endif
                                </button>
                            @endif
                            @if($serie->study->status == 'rejeitado' || ($serie->study->status == 'pendente' && Auth::user()->tipo == 'medico'))
                                <button 
                                    type="button"
                                    class="px-3 py-1.5 text-xs font-medium bg-primary/5 text-primary hover:bg-primary/15 rounded-md border border-primary/10 transition-all"
                                    @click="modalRejeicaoOpen = true"
                                >
                                    @if(Auth::user()->tipo == 'medico')
                                        {{ $serie->motivo_rejeicao ? 'Editar Rejeição' : 'Rejeitar Exame'}}
                                    @else
                                        {{ $serie->motivo_rejeicao ? 'Exame Rejeitado' : 'Sem Rejeição Cadastrada'}}
                                    @endif
                                </button>
                            @endif
                            @if(($serie->study->status == 'laudado' && $serie->protocolo()->exists()) && Auth::user()->tipo != 'medico')
                                <button
                                    type="button"
                                    wire:click="baixarProtocolo"
                                    wire:loading.attr="disabled"
                                    wire:target="baixarProtocolo"
                                    class="flex items-center gap-1 whitespace-nowrap px-2.5 py-1 text-[11px] font-medium rounded-md border transition-all
                                        bg-primary/5 text-primary hover:bg-primary/15 border-primary/10
                                        disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="baixarProtocolo">
                                        Download Protocolo
                                    </span>
                                    <span wire:loading wire:target="baixarProtocolo" class="flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3 text-primary" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
                                        </svg>
                                        Baixando...
                                    </span>
                                </button>
                            @elseif(($serie->laudo()->exists() && !$serie->protocolo()->exists()) && Auth::user()->tipo != 'medico')
                                <button
                                    type="button"
                                    wire:click="gerarProtocolo"
                                    wire:loading.attr="disabled"
                                    wire:target="gerarProtocolo"
                                    class="whitespace-nowrap px-2.5 py-1 text-[11px] font-medium rounded-md border transition-all
                                        bg-primary/5 text-primary hover:bg-primary/15 border-primary/10
                                        disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="gerarProtocolo">
                                        Gerar Protocolo
                                    </span>
                                    <span wire:loading wire:target="gerarProtocolo">
                                        Gerando...
                                    </span>
                                </button>
                            @endif
                            {{-- Botão Download --}}
                            @if($serie->laudo()->exists())
                                <button
                                    type="button"
                                    wire:click="baixarLaudo"
                                    wire:loading.attr="disabled"
                                    wire:target="baixarLaudo"
                                    class="flex items-center gap-1 whitespace-nowrap px-2.5 py-1 text-[11px] font-medium rounded-md border transition-all
                                        bg-primary/5 text-primary hover:bg-primary/15 border-primary/10
                                        disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="baixarLaudo">
                                        Download Laudo
                                    </span>
                                    <span wire:loading wire:target="baixarLaudo" class="flex items-center gap-1">
                                        <svg class="animate-spin h-3 w-3 text-primary" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
                                        </svg>
                                        Baixando...
                                    </span>
                                </button>
                            @endif
                            {{-- Botão Toggle --}}
                            <button
                                wire:click="toggleSerie({{ $serie->id }})"
                                class="whitespace-nowrap inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium bg-muted/50 text-foreground hover:bg-muted rounded-md border border-border/50 transition-colors"
                            >
                                <svg class="w-3 h-3 transition-transform {{ ($openSeries[$serie->id] ?? false) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span>{{ ($openSeries[$serie->id] ?? false) ? 'Ocultar' : 'Ver exames' }}</span>
                            </button>
                        </div>
                </div>
            </div>
            @if($openSeries[$serie->id] ?? false)
                <div class="mt-3 ml-12 pl-4 border-l-2 border-primary/20 space-y-2">
                    @forelse($serie->instance as $instance)
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
    <!-- Modal Rejeição -->
    <template x-teleport="body">
        <div 
            x-show="modalRejeicaoOpen"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center"
            style="display: none;"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div @click="modalRejeicaoOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div 
                class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-4xl mx-4 p-6"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            >
                <div class="flex items-center justify-between mb-4 border-b border-border pb-3">
                    <h3 class="text-xl font-semibold text-foreground flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Rejeitar exames / Enviar para retificação</span>
                    </h3>
                    <button @click="modalRejeicaoOpen = false" class="p-1 hover:bg-accent rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="setRejeicao">
                    <div class="mb-6">
                        <label for="rejeicao-{{ $serie->id }}" class="block text-sm font-medium text-foreground mb-2">
                            Conclusão <span x-show="isMedico" class="text-destructive">*</span>
                        </label>
                        
                        {{-- Modo Visualização (Para quem não é médico) --}}
                        <div x-show="!isMedico" class="w-full px-4 py-3 bg-muted/20 border border-border rounded-lg min-h-[300px] overflow-y-auto italic text-muted-foreground">
                            {{ $serie->motivo_rejeicao ?? 'O procedimento não foi recusado pelo Dr(a) responsável.' }}
                        </div>

                        {{-- Modo Edição (Apenas para Médicos) --}}
                        <textarea
                            x-show="isMedico"
                            id="rejeicao-{{ $serie->id }}"
                            wire:model="rejeicao"
                            placeholder="Descreva aqui o motivo da rejeição..."
                            class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[300px] resize-y text-foreground"
                            rows="12"
                        ></textarea>
                    </div>

                    <div class="flex gap-3 justify-end items-center">
                        <span class="text-xs text-muted-foreground mr-auto" wire:loading target="setRejeicao">
                            Processando...
                        </span>

                        <button
                            type="button"
                            @click="modalRejeicaoOpen = false"
                            class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors text-sm"
                        >
                            Fechar
                        </button>
                        
                        <button
                            x-show="isMedico"
                            type="submit"
                            class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm flex items-center gap-2"
                        >
                            <svg wire:loading.remove target="setRejeicao" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span wire:loading.remove target="setRejeicao">Salvar Rejeição</span>
                            <span wire:loading target="setRejeicao">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    {{-- MODAL DE LAUDO TÉCNICO --}}
    <template x-teleport="body">
        <div 
            x-show="modalLaudoOpen"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center"
            style="display: none;"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div @click="modalLaudoOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div 
                class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-4xl mx-4 p-6"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            >
                <div class="flex items-center justify-between mb-4 border-b border-border pb-3">
                    <h3 class="text-xl font-semibold text-foreground flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laudo Técnico / Médico</span>
                    </h3>
                    <button @click="modalLaudoOpen = false" class="p-1 hover:bg-accent rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="setLaudo">
                    <div class="mb-6">
                        <label for="laudo-{{ $serie->id }}" class="block text-sm font-medium text-foreground mb-2">
                            Conclusão Diagnóstica <span x-show="isMedico" class="text-destructive">*</span>
                        </label>
                        
                        {{-- Modo Visualização (Para quem não é médico) --}}
                        <div x-show="!isMedico" class="w-full px-4 py-3 bg-muted/20 border border-border rounded-lg min-h-[300px] overflow-y-auto italic text-muted-foreground">
                            {{ $serie->laudo()->where('ativo', true)->first()->laudo ?? 'O laudo ainda não foi emitido pelo médico responsável.' }}
                        </div>

                        {{-- Modo Edição (Apenas para Médicos) --}}
                        <textarea
                            x-show="isMedico"
                            id="laudo-{{ $serie->id }}"
                            wire:model="laudo"
                            placeholder="Descreva aqui o laudo detalhado..."
                            class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[300px] resize-y text-foreground"
                            rows="12"
                        ></textarea>
                    </div>

                    <div class="flex gap-3 justify-end items-center">
                        <span class="text-xs text-muted-foreground mr-auto" wire:loading target="setLaudo">
                            Processando...
                        </span>

                        <button
                            type="button"
                            @click="modalLaudoOpen = false"
                            class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors text-sm"
                        >
                            Fechar
                        </button>
                        
                        <button
                            x-show="isMedico"
                            type="submit"
                            class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm flex items-center gap-2"
                        >
                            <svg wire:loading.remove target="setLaudo" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span wire:loading.remove target="setLaudo">Salvar e Assinar Laudo</span>
                            <span wire:loading target="setLaudo">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>


</div>