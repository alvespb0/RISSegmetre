<div>
    <div class="p-6" x-data="{ 
        isMedico: {{ Auth::user()->tipo === 'medico' ? 'true' : 'false' }},
        openSocModal: null
    }">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-foreground mb-2">Lista de Exames</h2>
            <p class="text-muted-foreground">Gerencie e visualize todos os exames radiológicos</p>
        </div>

        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-6">            
            <div class="flex gap-3 flex-wrap">
                @foreach ([
                    'todos'     => 'Todos',
                    'pendente' => 'Pendentes',
                    'rejeitado'  => 'Rejeitados',
                    'laudado' => 'Laudados',
                ] as $key => $label)
                    <button
                        wire:click="setFiltro('{{ $key }}')"
                        class="
                            px-4 py-2 text-sm font-medium rounded-lg transition-colors border
                            {{ $filtro === $key
                                ? 'bg-primary text-primary-foreground border-primary'
                                : 'bg-card text-foreground border-border hover:bg-accent'
                            }}
                        "
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.500ms="filtroPaciente"
                        class="w-full pl-9 pr-3 py-2 text-sm bg-card border border-border text-foreground rounded-lg focus:ring-1 focus:ring-primary focus:border-primary placeholder-muted-foreground transition-colors"
                        placeholder="Buscar paciente..."
                    >
                </div>

                <div class="w-full sm:w-auto">
                    <input 
                        type="date" 
                        wire:model.blur="filtroStudyDate"
                        class="w-full sm:w-auto px-3 py-2 text-sm bg-card border border-border text-foreground rounded-lg focus:ring-1 focus:ring-primary focus:border-primary transition-colors cursor-pointer"
                    >
                </div>

                <button
                    wire:click="getExames" 
                    class="inline-flex justify-center items-center gap-2 px-3 py-2 text-sm font-medium bg-card text-foreground border border-border rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors shadow-sm whitespace-nowrap"
                    title="Atualizar lista"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="sm:hidden lg:inline">Atualizar</span>
                </button>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg overflow-hidden">
            @if(count($studies) > 0)
                <table class="w-full">
    <thead>
        <tr class="border-b border-border bg-muted/50">
            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Nome do Paciente</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Sexo do Paciente</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Data do Estudo</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Médico Solicitante</th>
            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Status</th>
            <th class="px-6 py-4 text-center text-sm font-medium text-muted-foreground">Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($studies as $study)
            <tr class="border-b border-border hover:bg-accent/50 transition-colors">
                <td class="px-6 py-4">
                    <span class="font-semibold text-foreground">{{ $study->patient->nome }}</span>
                </td>
                <td class="px-6 py-4 text-foreground">{{ $study->patient->sexo ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-foreground">{{ $study->study_date ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-foreground">{{ $study->solicitante ?? 'N/A' }}</td>
                
                <td class="px-6 py-4">
                    @php
                        $statusStyles = [
                            'pendente' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800',
                            'laudado'  => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
                            'rejeitado' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                        ];
                        $currentStatus = strtolower($study->status ?? 'pendente');
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusStyles[$currentStatus] ?? $statusStyles['pendente'] }}">
                        {{ ucfirst($currentStatus) }}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <button
                            wire:click="toggleStudy({{ $study->id }})"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 transition-transform {{ ($openStudies[$study->id] ?? false) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            {{ ($openStudies[$study->id] ?? false) ? 'Ocultar Séries' : 'Expandir Séries' }}
                        </button>
                        <button
                            type="button"
                            @click="openSocModal = {{ $study->id }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            Vincular SOC
                        </button>
                    </div>
                </td>
            </tr>

            @if($openStudies[$study->id] ?? false)
            <tr>
                <td colspan="6" class="px-6 py-4 bg-gradient-to-r from-muted/20 via-muted/10 to-transparent">
                    <div class="ml-4 pl-4 border-l-2 border-primary/30 space-y-3">
                        @forelse($study->serie as $serie)
                            <livewire:SeriesList :serie="$serie" :filtro="$filtro" :wire:key="'serie-'.$serie->id"/>
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

        {{-- Modal Vincular Registro SOC --}}
        <div
            x-show="openSocModal !== null"
            x-cloak
            x-on:keydown.escape.window="openSocModal = null"
            class="fixed inset-0 z-50 overflow-y-auto px-4"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="fixed inset-0 bg-black/50" @click="openSocModal = null"></div>
            <div class="relative min-h-[200px] flex items-center justify-center p-4">
                <div
                    class="relative w-full max-w-4xl bg-card border border-border rounded-lg shadow-xl"
                    @click.stop
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                >
                    <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                        <h3 class="text-lg font-semibold text-foreground">Vincular Registro SOC</h3>
                        <button
                            type="button"
                            @click="openSocModal = null"
                            class="p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-lg transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 max-h-[70vh] overflow-y-auto">
                        @foreach($studies as $study)
                            <div x-show="openSocModal == {{ $study->id }}" x-cloak>
                                <livewire:empresas-soc-list :study="$study" :key="'soc-list-'.$study->id" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

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
<style>
        [x-cloak] { display: none !important; }
    </style>
</div>