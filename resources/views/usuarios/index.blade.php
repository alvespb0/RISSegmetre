<x-app-layout>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-foreground mb-2">Usuários</h2>
                <p class="text-muted-foreground">Gerencie os acessos e permissões dos profissionais do sistema</p>
            </div>
            
            <a
                href="{{ route('register') }}"
                class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium inline-flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Novo Usuário
            </a>
        </div>

        <div class="bg-card border border-border rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-sidebar-accent/30 border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-foreground">Usuário</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Email</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Perfil / Tipo</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Data de Cadastro</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($users as $user)
                            <tr class="hover:bg-sidebar-accent/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-foreground">{{ $user->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center text-muted-foreground font-mono text-xs">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                        $typeStyles = [
                                            'admin'   => 'bg-purple-500/10 text-purple-600 border-purple-200',
                                            'medico'  => 'bg-blue-500/10 text-blue-600 border-blue-200',
                                            'tecnico' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200',
                                            'dev'     => 'bg-gray-500/10 text-gray-600 border-gray-200',
                                        ];
                                        $typeLabels = [
                                            'admin'   => 'Administrador',
                                            'medico'  => 'Médico',
                                            'tecnico' => 'Técnico',
                                            'dev'     => 'Desenvolvedor',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $typeStyles[$user->tipo] ?? 'bg-sidebar-accent text-foreground' }}">
                                        {{ $typeLabels[$user->tipo] ?? ucfirst($user->tipo) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center text-muted-foreground">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a
                                            href="{{route('usuarios.edit', $user->id)}}"
                                            class="px-3 py-1.5 text-sm text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                        >
                                            Editar
                                        </a>
                                        <form action="{{route('usuarios.delete')}}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente remover este acesso?');">
                                            @csrf
                                            <input type="hidden" name="userId" value="{{$user->id}}">
                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 text-sm text-destructive hover:bg-destructive/10 rounded-lg transition-colors"
                                            >
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-muted-foreground italic">
                                    Nenhum usuário cadastrado no sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>