<div>
    <div class="p-6" x-data="{ 
        modalRejeicaoOpen: false, 
        selectedExameId: null, 
        justificativa: '',
        modalAnamneseOpen: false,
        selectedInstanceId: null,
        anamneseTexto: '',
        isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},
        abrirAnamnese(instanceId, anamnese) {
            this.selectedInstanceId = instanceId;
            this.anamneseTexto = anamnese || '';
            this.modalAnamneseOpen = true;
            // Atualiza o Livewire quando o modal abre
            $wire.instanceId = instanceId;
            $wire.anamnese = anamnese || '';
        }
    }">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-foreground mb-2">Lista de Exames</h2>
            <p class="text-muted-foreground">Gerencie e visualize todos os exames radiológicos</p>
        </div>

        <!-- Filtros rápidos -->
        <div class="flex gap-3 mb-6 flex-wrap">
            @foreach ([
                'todos'     => 'Todos os Exames',
                'pendente' => 'Pendentes',
                'rejeitado'  => 'Rejeitados',
                'laudado' => 'Laudados',
            ] as $key => $label)
                <button
                    wire:click="setFiltro('{{ $key }}')"
                    class="
                        px-4 py-2 rounded-lg transition-colors
                        {{ $filtro === $key
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-foreground border border-border hover:bg-accent'
                        }}
                    "
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Tabela de dados -->
        <div class="bg-card border border-border rounded-lg overflow-hidden">
            @if(count($studies) > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-muted/50">
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Nome do Paciente</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Sexo do Paciente</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Data do Estudo</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Médico Solicitante</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studies as $study)
                            <tr class="border-b border-border hover:bg-accent/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-foreground">{{ $study->patient->nome }}</span>
                                </td>
                                <td class="px-6 py-4 text-foreground">{{ $study->patient->sexo ?? N/A }}</td>
                                <td class="px-6 py-4 text-foreground">{{ $study->study_date ?? N/A }}</td>
                                <td class="px-6 py-4 text-foreground">{{ $study->solicitante ?? N/A }}</td>
                                <td class="px-6 py-4">
                                    <button
                                        wire:click="toggleStudy({{ $study->id }})"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4 transition-transform {{ ($openStudies[$study->id] ?? false) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                        {{ ($openStudies[$study->id] ?? false)
                                            ? 'Ocultar Séries'
                                            : 'Expandir Séries' }}
                                    </button>
                                </td>
                            </tr>
                            @if($openStudies[$study->id] ?? false)
                            <tr>
                                <td colspan="5" class="px-6 py-4 bg-gradient-to-r from-muted/20 via-muted/10 to-transparent">
                                    <div class="ml-4 pl-4 border-l-2 border-primary/30 space-y-3">
                                        @forelse($study->serie as $serie)
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
                                                                                                data-instance-id="{{ $instance->id }}"
                                                                                                data-anamnese="{{ base64_encode($instance->anamnese ?? '') }}"
                                                                                                @click="abrirAnamnese(parseInt($el.dataset.instanceId), atob($el.dataset.anamnese || ''))"
                                                                                                class="mt-1 text-xs text-primary hover:underline font-medium"
                                                                                            >
                                                                                                {{ $instance->anamnese ? 'Visualizar/Editar Anamnese' : 'Escrever Anamnese' }}
                                                                                            </button>
                                                                                        @else
                                                                                            @if($instance->anamnese)
                                                                                                <button 
                                                                                                    data-instance-id="{{ $instance->id }}"
                                                                                                    data-anamnese="{{ base64_encode($instance->anamnese) }}"
                                                                                                    @click="abrirAnamnese(parseInt($el.dataset.instanceId), atob($el.dataset.anamnese))"
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
                                        @empty
                                            <div class="ml-6 pl-4 py-3 text-sm text-muted-foreground italic bg-card/50 border border-border/50 rounded-lg">
                                                Nenhuma série encontrada para este estudo
                                            </div>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-muted-foreground mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-muted-foreground text-lg">Nenhum exame encontrado</p>
                    <p class="text-muted-foreground text-sm mt-2">Tente alterar o filtro ou verifique novamente mais tarde.</p>
                </div>
            @endif
        </div>

        <!-- Paginação -->
        @if($studies->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ $studies->firstItem() }} a {{ $studies->lastItem() }} de {{ $studies->total() }} resultados
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Botão Anterior --}}
                    @if($studies->onFirstPage())
                        <span class="px-3 py-2 text-sm font-medium text-muted-foreground bg-muted/50 border border-border rounded-lg cursor-not-allowed">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Anterior
                        </span>
                    @else
                        <button wire:click="previousPage" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Anterior
                        </button>
                    @endif

                    {{-- Números das páginas --}}
                    <div class="flex items-center gap-1">
                        @foreach($studies->getUrlRange(max(1, $studies->currentPage() - 2), min($studies->lastPage(), $studies->currentPage() + 2)) as $page => $url)
                            @if($page == $studies->currentPage())
                                <span class="px-3 py-2 text-sm font-medium text-primary-foreground bg-primary border border-primary rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- Botão Próximo --}}
                    @if($studies->hasMorePages())
                        <button wire:click="nextPage" class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition-colors">
                            Próximo
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-muted-foreground bg-muted/50 border border-border rounded-lg cursor-not-allowed">
                            Próximo
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Modal de Rejeição -->
        <div 
            x-show="modalRejeicaoOpen"
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
            <!-- Backdrop -->
            <div 
                @click="modalRejeicaoOpen = false; justificativa = ''; selectedExameId = null"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            <!-- Modal -->
            <div 
                class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-md mx-4 p-6"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-foreground">Rejeitar Exame</h3>
                    <button
                        @click="modalRejeicaoOpen = false; justificativa = ''; selectedExameId = null"
                        class="p-1 hover:bg-accent rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="mb-6">
                    <label for="justificativa" class="block text-sm font-medium text-foreground mb-2">
                        Justificativa Técnica <span class="text-destructive">*</span>
                    </label>
                    <textarea
                        id="justificativa"
                        x-model="justificativa"
                        placeholder="Descreva o motivo da rejeição do exame (ex: qualidade inadequada, posicionamento incorreto, artefatos...)"
                        class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[120px] resize-y"
                        rows="5"
                    ></textarea>
                    <p class="text-xs text-muted-foreground mt-2">
                        Esta justificativa será registrada no sistema e enviada ao técnico responsável.
                    </p>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 justify-end">
                    <button
                        @click="modalRejeicaoOpen = false; justificativa = ''; selectedExameId = null"
                        class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="if(justificativa.trim()) { confirmarRejeicao(selectedExameId, justificativa); }"
                        :disabled="!justificativa.trim()"
                        class="px-4 py-2 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Confirmar Rejeição
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Anamnese -->
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
            <!-- Backdrop -->
            <div 
                @click="modalAnamneseOpen = false; anamneseTexto = ''; selectedInstanceId = null"
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            <!-- Modal -->
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
                <!-- Header -->
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
                    <!-- Body -->
                    <div class="mb-6">
                        <label for="anamnese" class="block text-sm font-medium text-foreground mb-2">
                            Anamnese <span x-show="!isMedico" class="text-destructive">*</span>
                        </label>
                        <template x-if="isMedico">
                            <div class="w-full px-4 py-3 bg-input-background border border-border rounded-lg min-h-[200px] max-h-[400px] overflow-y-auto">
                                <p class="text-foreground whitespace-pre-wrap" x-text="anamneseTexto || 'Nenhuma anamnese registrada'"></p>
                            </div>
                        </template>
                        <input type="hidden" wire:model="instanceId">
                        <template x-if="!isMedico">
                            <textarea
                                id="anamnese"
                                wire:model="anamnese"
                                placeholder="Descreva a anamnese do paciente (sintomas, histórico, queixas principais, etc.)"
                                class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[200px] resize-y"
                                rows="8"
                            ></textarea>
                        </template>
                        <p class="text-xs text-muted-foreground mt-2">
                            <span x-show="!isMedico">Esta anamnese será registrada no sistema e associada ao exame.</span>
                            <span x-show="isMedico">Visualização apenas. Você não pode editar a anamnese.</span>
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="flex gap-3 justify-end">
                        <button
                            type="button"
                            @click="modalAnamneseOpen = false; anamneseTexto = ''; selectedInstanceId = null"
                            class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors"
                        >
                            Fechar
                        </button>
                        <template x-if="!isMedico">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                            >
                                Salvar Anamnese
                            </button>
                        </template>
                    </div>
                </form>
                </div>
            </div>
        </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        function visualizarExame(id) {
            console.log('Visualizar exame no Weasis:', id);
            alert(`Abrindo exame ${id} no Weasis Viewer...`);
        }

        function escreverLaudo(id) {
            console.log('Escrever laudo para exame:', id);
            alert(`Abrindo editor de laudo para exame ${id}...`);
        }

        function confirmarRejeicao(id, justificativa) {
            console.log('Rejeitar exame:', id, 'Justificativa:', justificativa);
            alert(`Exame ${id} rejeitado.\nJustificativa: ${justificativa}`);
            // Aqui você pode fazer uma requisição AJAX para salvar no backend
        }
    </script>
</div>
