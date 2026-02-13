<x-app-layout>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-foreground mb-2">Pacientes</h2>
                <p class="text-muted-foreground">
                    Gerencie os cadastros de pacientes e visualize seus exames
                </p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-sidebar-accent/30 border-b border-border">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-foreground">Paciente</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">CPF</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Sexo</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Nascimento</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Estudos</th>
                            <th class="px-6 py-4 text-center font-semibold text-foreground">Cadastro</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-border">
                        @forelse($patients as $patient)

                            @php
                                $sexoStyles = [
                                    'M' => 'bg-blue-500/10 text-blue-600 border-blue-200',
                                    'F' => 'bg-pink-500/10 text-pink-600 border-pink-200',
                                ];
                            @endphp

                            <tr class="hover:bg-sidebar-accent/10 transition-colors">

                                {{-- Nome --}}
                                <td class="px-6 py-4 font-medium text-foreground">
                                    {{ $patient->nome }}
                                </td>

                                {{-- CPF --}}
                                <td class="px-6 py-4 text-center font-mono text-xs text-muted-foreground">
                                    {{ $patient->patient_cpf ?? '—' }}
                                </td>

                                {{-- Sexo --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border
                                        {{ $sexoStyles[$patient->sexo] ?? 'bg-sidebar-accent text-foreground' }}">
                                        {{ $patient->sexo ?? '—' }}
                                    </span>
                                </td>

                                {{-- Nascimento --}}
                                <td class="px-6 py-4 text-center text-muted-foreground">
                                    {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Estudos --}}
                                <td class="px-6 py-4 text-center font-semibold text-primary">
                                    {{ $patient->study_count }}
                                </td>

                                {{-- Cadastro --}}
                                <td class="px-6 py-4 text-center text-muted-foreground">
                                    {{ $patient->created_at->format('d/m/Y') }}
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-muted-foreground italic">
                                    Nenhum paciente cadastrado.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
                @if($patients->hasPages())
                    <div class="mt-6 px-6 py-4 flex items-center justify-between border-t border-border">

                        <div class="text-sm text-muted-foreground">
                            Mostrando {{ $patients->firstItem() }}
                            a {{ $patients->lastItem() }}
                            de {{ $patients->total() }} resultados
                        </div>

                        <div class="flex items-center gap-2">

                            {{-- Anterior --}}
                            @if($patients->onFirstPage())
                                <span class="px-3 py-2 text-sm font-medium text-muted-foreground bg-muted/50 border border-border rounded-lg cursor-not-allowed">
                                    ← Anterior
                                </span>
                            @else
                                <a href="{{ $patients->previousPageUrl() }}"
                                class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition">
                                    ← Anterior
                                </a>
                            @endif

                            {{-- Números --}}
                            <div class="flex gap-1">

                                @foreach($patients->getUrlRange(
                                    max(1, $patients->currentPage()-2),
                                    min($patients->lastPage(), $patients->currentPage()+2)
                                ) as $page => $url)

                                    @if($page == $patients->currentPage())
                                        <span class="px-3 py-2 text-sm font-medium bg-primary text-primary-foreground rounded-lg">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}"
                                        class="px-3 py-2 text-sm font-medium bg-card border border-border rounded-lg hover:bg-accent transition">
                                            {{ $page }}
                                        </a>
                                    @endif

                                @endforeach

                            </div>

                            {{-- Próximo --}}
                            @if($patients->hasMorePages())
                                <a href="{{ $patients->nextPageUrl() }}"
                                class="px-3 py-2 text-sm font-medium text-foreground bg-card border border-border rounded-lg hover:bg-accent transition">
                                    Próximo →
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm font-medium text-muted-foreground bg-muted/50 border border-border rounded-lg cursor-not-allowed">
                                    Próximo →
                                </span>
                            @endif

                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
