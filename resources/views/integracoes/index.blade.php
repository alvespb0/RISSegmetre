<x-app-layout>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-foreground mb-2">Integrações</h2>
                <p class="text-muted-foreground">Gerencie as integrações de sistemas externos</p>
            </div>
            <a
                href="{{ route('dev.integracoes.create') }}"
                class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium inline-flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nova Integração
            </a>
        </div>

        <div class="bg-card border border-border rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-sidebar-accent/30 border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Sistema</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Slug</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Tipo</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Auth</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Endpoint</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-foreground">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($integracoes as $integracao)
                            <tr class="hover:bg-sidebar-accent/10 transition-colors {{ $integracao->trashed() ? 'opacity-75' : '' }}">
                                <td class="px-6 py-4">
                                    @if($integracao->trashed())
                                        <span class="px-2 py-1 text-xs rounded bg-destructive/10 text-destructive">Inativo</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-[#10b981]/10 text-[#10b981]">Ativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-foreground">{{ $integracao->sistema }}</p>
                                        @if($integracao->descricao)
                                            <p class="text-sm text-muted-foreground">{{ Str::limit($integracao->descricao, 40) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="text-sm bg-sidebar-accent/50 px-2 py-1 rounded">{{ $integracao->slug }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded {{ $integracao->tipo === 'rest' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600' }}">
                                        {{ strtoupper($integracao->tipo) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded bg-sidebar-accent text-foreground">
                                        {{ $integracao->auth }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground max-w-xs truncate">
                                    {{ $integracao->endpoint }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$integracao->trashed())
                                            <a
                                                href="{{ route('dev.integracoes.edit', $integracao) }}"
                                                class="px-3 py-1.5 text-sm text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                            >
                                                Editar
                                            </a>
                                            <form
                                                action="{{ route('dev.integracoes.destroy', $integracao) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Tem certeza que deseja inativar esta integração?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-sm text-destructive hover:bg-destructive/10 rounded-lg transition-colors"
                                                >
                                                    Inativar
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('dev.integracoes.restore', $integracao->id) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Tem certeza que deseja ativar esta integração?');"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-sm text-[#10b981] hover:bg-[#10b981]/10 rounded-lg transition-colors"
                                                >
                                                    Ativar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-muted-foreground">
                                    Nenhuma integração cadastrada.
                                    <a href="{{ route('dev.integracoes.create') }}" class="text-primary hover:underline ml-1">Criar primeira integração</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
