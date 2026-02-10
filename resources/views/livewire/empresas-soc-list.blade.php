<div>
    <div class="mb-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border text-foreground rounded-lg focus:ring-1 focus:ring-primary focus:border-primary placeholder-muted-foreground transition-colors"
                placeholder="Buscar por nome, código SOC ou CNPJ..."
            >
        </div>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-border bg-muted/50">
                <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Nome</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">Código SOC</th>
                <th class="px-6 py-4 text-left text-sm font-medium text-muted-foreground">CNPJ</th>
                <th class="px-6 py-4 text-center text-sm font-medium text-muted-foreground">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($empresas as $empresa)
                <tr class="border-b border-border hover:bg-accent/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-foreground">{{ $empresa->nome }}</td>
                    <td class="px-6 py-4 text-foreground">
                        <code class="text-sm bg-muted px-2 py-1 rounded">{{ $empresa->codigo_soc }}</code>
                    </td>
                    <td class="px-6 py-4 text-foreground">{{ $empresa->cnpj ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <button
                            type="button"
                            wire:click="getCodigoSeq({{ $empresa->codigo_soc }})"
                            wire:loading.attr="disabled"
                            class="px-3 py-1.5 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg transition-colors flex items-center gap-2"
                        >
                            <!-- Texto normal -->
                            <span wire:loading.remove wire:target="getCodigoSeq">
                                Vincular
                            </span>

                            <!-- Loading -->
                            <span wire:loading wire:target="getCodigoSeq" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                        fill="none"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                    ></path>
                                </svg>

                                Vinculando...
                            </span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                        @if($search)
                            Nenhuma empresa encontrada para "{{ $search }}".
                        @else
                            Nenhuma empresa SOC cadastrada.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
