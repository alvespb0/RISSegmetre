<x-app-layout>
    <div class="p-6 space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-foreground mb-2">Dashboard</h2>
            <p class="text-muted-foreground">Visão geral do sistema de radiologia</p>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Exames Pendentes</p>
                        <p class="text-3xl font-semibold text-foreground">{{ $stats['exames_pendentes'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#eab308]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#eab308]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Exames Hoje</p>
                        <p class="text-3xl font-semibold text-foreground">{{ $stats['exames_hoje'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Laudos Concluídos</p>
                        <p class="text-3xl font-semibold text-foreground">{{ $stats['laudos_concluidos'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-[#10b981]/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted-foreground text-sm mb-2">Exames Rejeitados</p>
                        <p class="text-3xl font-semibold text-destructive">{{ $stats['exames_rejeitados'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-destructive/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Exames ao Longo do Tempo -->
            <div class="bg-card border border-border rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-foreground mb-1">Exames e Laudos - Última Semana</h3>
                    <p class="text-sm text-muted-foreground">Acompanhamento diário de exames realizados e laudos concluídos</p>
                </div>
                <div class="h-64">
                    <canvas 
                        id="examesTempoChart"
                        data-labels="{{ json_encode($examesTempo['labels']) }}"
                        data-exames="{{ json_encode($examesTempo['data']) }}"
                        data-laudos="{{ json_encode($examesTempo['laudados']) }}"
                    ></canvas>
                </div>
            </div>

            <!-- Gráfico de Exames por Status -->
            <div class="bg-card border border-border rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-foreground mb-1">Distribuição por Status</h3>
                    <p class="text-sm text-muted-foreground">Visão geral dos exames por status atual</p>
                </div>
                <div class="h-64">
                    <canvas 
                        id="examesStatusChart"
                        data-labels="{{ json_encode($examesStatus['labels']) }}"
                        data-values="{{ json_encode($examesStatus['data']) }}"
                    ></canvas>
                </div>
            </div>

            <!-- Gráfico de Exames por Modalidade -->
            <div class="bg-card border border-border rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-foreground mb-1">Partes Mais Examinadas</h3>
                    <p class="text-sm text-muted-foreground">Quantidade de exames realizados por parte do corpo</p>
                </div>
                <div class="h-64">
                    <canvas 
                        id="examesModalidadeChart"
                        data-labels="{{ json_encode($examesBodyPartExamined['labels']) }}"
                        data-values="{{ json_encode($examesBodyPartExamined['data']) }}"
                    ></canvas>
                </div>
            </div>

            <!-- Gráfico de Performance de Laudos -->
            <div class="bg-card border border-border rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-foreground mb-1">Performance de Laudos</h3>
                    <p class="text-sm text-muted-foreground">Tempo médio de conclusão de laudos (em horas)</p>
                </div>
                <div class="h-64">
                    <canvas 
                        id="examesRejeitosChart"
                        data-labels="{{ json_encode($performanceLaudos['labels']) }}"
                        data-values="{{ json_encode($performanceLaudos['data']) }}"
                    ></canvas>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

