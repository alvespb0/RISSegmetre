@php
    $statusConfig = [
        'laudado' => ['bg' => 'bg-[#10b981]/10', 'text' => 'text-[#10b981]', 'label' => 'Laudado'],
        'pendente' => ['bg' => 'bg-[#eab308]/10', 'text' => 'text-[#eab308]', 'label' => 'Pendente'],
        'rejeitado' => ['bg' => 'bg-[#ef4444]/10', 'text' => 'text-[#ef4444]', 'label' => 'Rejeitado'],
        'urgente' => ['bg' => 'bg-[#f97316]/10', 'text' => 'text-[#f97316]', 'label' => 'Urgente'],
    ];
@endphp

<x-app-layout>
    <div class="p-6" x-data="{ modalRejeicaoOpen: false, selectedExameId: null, justificativa: '' }">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-foreground mb-2">Lista de Exames</h2>
            <p class="text-muted-foreground">Gerencie e visualize todos os exames radiológicos</p>
        </div>

        <!-- Filtros rápidos -->
        <div class="flex gap-3 mb-6 flex-wrap">
            @php
                $filtroTodosClasses = $filtro === 'todos' 
                    ? 'px-4 py-2 rounded-lg transition-colors bg-primary text-primary-foreground'
                    : 'px-4 py-2 rounded-lg transition-colors bg-card text-foreground border border-border hover:bg-accent';
            @endphp
            <a href="{{ route('exames.index', ['filtro' => 'todos']) }}" class="{{ $filtroTodosClasses }}">
                Todos os Exames
            </a>

            @php
                $filtroPendentesClasses = $filtro === 'pendentes' 
                    ? 'px-4 py-2 rounded-lg transition-colors bg-primary text-primary-foreground'
                    : 'px-4 py-2 rounded-lg transition-colors bg-card text-foreground border border-border hover:bg-accent';
            @endphp
            <a href="{{ route('exames.index', ['filtro' => 'pendentes']) }}" class="{{ $filtroPendentesClasses }}">
                Pendentes Hoje
            </a>

            @php
                $filtroUrgentesClasses = $filtro === 'urgentes' 
                    ? 'px-4 py-2 rounded-lg transition-colors bg-primary text-primary-foreground'
                    : 'px-4 py-2 rounded-lg transition-colors bg-card text-foreground border border-border hover:bg-accent';
            @endphp
            <a href="{{ route('exames.index', ['filtro' => 'urgentes']) }}" class="{{ $filtroUrgentesClasses }}">
                Urgentes
            </a>

            @php
                $filtroMeusClasses = $filtro === 'meus' 
                    ? 'px-4 py-2 rounded-lg transition-colors bg-primary text-primary-foreground'
                    : 'px-4 py-2 rounded-lg transition-colors bg-card text-foreground border border-border hover:bg-accent';
            @endphp
            <a href="{{ route('exames.index', ['filtro' => 'meus']) }}" class="{{ $filtroMeusClasses }}">
                Meus Laudos
            </a>
        </div>

        <!-- Tabela de dados -->
        <div class="bg-card border border-border rounded-lg overflow-hidden">
            @if(count($exames) > 0)
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-muted/50">
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Nome do Paciente</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Data do Exame</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Modalidade</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Médico Solicitante</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exames as $exame)
                            <tr class="border-b border-border hover:bg-accent/50 transition-colors">
                                <td class="px-6 py-4">
                                    @php
                                        $status = $statusConfig[$exame['status']] ?? $statusConfig['pendente'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm {{ $status['bg'] }} {{ $status['text'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-foreground">{{ $exame['nome_paciente'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-foreground">{{ $exame['data_exame'] }}</td>
                                <td class="px-6 py-4 text-foreground">{{ $exame['modalidade'] }}</td>
                                <td class="px-6 py-4 text-foreground">{{ $exame['medico_solicitante'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button
                                            onclick="visualizarExame('{{ $exame['id'] }}')"
                                            class="p-2 hover:bg-primary/10 text-primary rounded-lg transition-colors"
                                            title="Visualizar no Weasis"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        @if($exame['status'] !== 'laudado')
                                            <button
                                                onclick="escreverLaudo('{{ $exame['id'] }}')"
                                                class="p-2 hover:bg-secondary/10 text-secondary rounded-lg transition-colors"
                                                title="Escrever Laudo"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button
                                            @click="selectedExameId = '{{ $exame['id'] }}'; modalRejeicaoOpen = true"
                                            class="p-2 hover:bg-destructive/10 text-destructive rounded-lg transition-colors"
                                            title="Rejeitar Exame"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
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
</x-app-layout>

