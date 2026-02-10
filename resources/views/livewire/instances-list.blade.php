<div 
    x-data="{ 
        modalAnamneseOpen: false,
        isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},
        init() {
            // Escuta eventos do Livewire para fechar os modais após as ações
            Livewire.on('close-modal-anamnese-{{ $instance->id }}', () => {
                this.modalAnamneseOpen = false;
            });
        }
    }"
    class="block" 
>
    {{-- Card de Informações do Exame --}}
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
                        
                        {{-- Gatilho para Modal de Anamnese --}}
                        <button 
                            type="button"
                            @click="modalAnamneseOpen = true"
                            class="mt-1 text-xs text-primary hover:underline font-medium"
                        >
                            @if(Auth::user()->tipo !== 'medico')
                                {{ $instance->anamnese ? 'Visualizar/Editar Anamnese' : 'Escrever Anamnese' }}
                            @else
                                {{ $instance->anamnese ? 'Visualizar Anamnese' : 'Nenhuma anamnese registrada' }}
                            @endif
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Botão Download DCM --}}
                    <button 
                        class="p-1.5 text-primary hover:bg-primary/10 rounded-md transition-colors" 
                        wire:click="downloadDCM" 
                        title="Baixar Arquivo DICOM"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    @if(Auth::user()->tipo !== 'medico')
                        <button
                            wire:click="liberarExame"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md transition-colors
                                {{ $liberadoTec
                                    ? 'bg-primary/10 text-primary border border-primary/20'
                                    : 'bg-muted text-muted-foreground border border-border hover:bg-accent' }}"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $liberadoTec ? 'Liberado' : 'Liberar' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="ml-4 pl-3 pr-3 py-2.5 bg-muted/30 border border-border/50 rounded-md hover:bg-muted/50 hover:border-primary/30 transition-all duration-150">
            <p class="text-sm text-muted-foreground italic">Exame aguardando liberação do técnico.</p>
        </div>
    @endif

    {{-- MODAL DE ANAMNESE --}}
    <template x-teleport="body">
        <div 
            x-show="modalAnamneseOpen"
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
            <div @click="modalAnamneseOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div 
                class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-2xl mx-4 p-6"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-foreground">
                        <span x-text="isMedico ? 'Visualizar Anamnese' : 'Escrever/Visualizar Anamnese'"></span>
                    </h3>
                    <button @click="modalAnamneseOpen = false" class="p-1 hover:bg-accent rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="setAnamnese">
                    <div class="mb-6">
                        <label for="anamnese-{{ $instance->id }}" class="block text-sm font-medium text-foreground mb-2">
                            Anamnese <span x-show="!isMedico" class="text-destructive">*</span>
                        </label>
                        
                        {{-- Modo Visualização (Médicos) --}}
                        <div x-show="isMedico" class="w-full px-4 py-3 bg-input-background border border-border rounded-lg min-h-[200px] max-h-[400px] overflow-y-auto">
                            <p class="text-foreground whitespace-pre-wrap">{{ $instance->anamnese ?? 'Nenhuma anamnese registrada' }}</p>
                        </div>

                        {{-- Modo Edição (Técnicos) --}}
                        <textarea
                            x-show="!isMedico"
                            id="anamnese-{{ $instance->id }}"
                            wire:model="anamnese"
                            placeholder="Descreva a anamnese clínica..."
                            class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[200px] resize-y"
                            rows="8"
                        ></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button
                            type="button"
                            @click="modalAnamneseOpen = false"
                            class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors"
                        >
                            Fechar
                        </button>
                        <button
                            x-show="!isMedico"
                            type="submit"
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            <span wire:loading.remove target="setAnamnese">Salvar Anamnese</span>
                            <span wire:loading target="setAnamnese">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>