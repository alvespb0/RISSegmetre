<div>
    <div class="p-6" x-data="{ 
        isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},

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
                                            <livewire:SeriesList :serie="$serie" :filtro="$filtro" :wire:key="$serie->id"/>
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
