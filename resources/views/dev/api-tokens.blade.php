<x-app-layout>
    <div class="p-6 space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-foreground mb-2">Gerenciamento de Tokens de API</h2>
            <p class="text-muted-foreground">Gere e gerencie tokens de API para integrações</p>
        </div>

        <!-- Formulário de Geração de Token -->
        <div class="bg-card border border-border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Gerar Novo Token</h3>
            <form id="tokenForm" class="space-y-4">
                @csrf
                <div>
                    <label for="tokenName" class="block text-sm font-medium text-foreground mb-2">
                        Nome do Token <span class="text-destructive">*</span>
                    </label>
                    <input
                        type="text"
                        id="tokenName"
                        name="name"
                        required
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="Ex: Integração Sistema X"
                    />
                </div>

                <div>
                    <label for="tokenCnpj" class="block text-sm font-medium text-foreground mb-2">
                        CNPJ <span class="text-destructive">*</span>
                    </label>
                    <input
                        type="text"
                        id="tokenCnpj"
                        name="cnpj"
                        required
                        maxlength="18"
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        placeholder="00.000.000/0000-00"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium"
                >
                    Gerar Token
                </button>
            </form>
        </div>

        <!-- Token Gerado (exibido após geração) -->
        <div id="generatedTokenContainer" class="hidden bg-card border border-border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Token Gerado com Sucesso!</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Token de API</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            id="generatedToken"
                            readonly
                            class="flex-1 px-4 py-2 border border-border rounded-lg bg-background text-foreground font-mono text-sm"
                        />
                        <button
                            type="button"
                            id="copyTokenBtn"
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                        >
                            Copiar
                        </button>
                    </div>
                    <p class="text-xs text-muted-foreground mt-2">
                        ⚠️ Guarde este token com segurança. Ele não será exibido novamente.
                    </p>
                </div>
            </div>
        </div>

        <!-- Lista de Tokens Gerados -->
        <div class="bg-card border border-border rounded-lg p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Tokens Gerados</h3>
            <div class="space-y-3">
                @forelse($tokens as $token)
                    <div class="border border-border rounded-lg p-4 hover:bg-sidebar-accent/10 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-semibold text-foreground">{{ $token->name }}</h4>
                                    @if($token->active)
                                        <span class="px-2 py-1 text-xs bg-[#10b981]/10 text-[#10b981] rounded">Ativo</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-destructive/10 text-destructive rounded">Inativo</span>
                                    @endif
                                </div>
                                @if($token->empresa)
                                    <p class="text-sm text-muted-foreground mb-1">
                                        <strong>Empresa:</strong> {{ $token->empresa->nome }}
                                    </p>
                                    <p class="text-sm text-muted-foreground mb-1">
                                        <strong>CNPJ:</strong> {{ $token->empresa->cnpj }}
                                    </p>
                                @endif
                                <p class="text-xs text-muted-foreground">
                                    Criado em: {{ $token->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <button
                                type="button"
                                onclick="toggleToken({{ $token->id }})"
                                class="px-3 py-1 text-sm {{ $token->active ? 'text-destructive hover:bg-destructive/10' : 'text-[#10b981] hover:bg-[#10b981]/10' }} rounded transition-colors"
                            >
                                {{ $token->active ? 'Inativar' : 'Ativar' }}
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-muted-foreground text-sm">Nenhum token gerado ainda.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Função para formatar CNPJ
        function formatCNPJ(value) {
            const numbers = value.replace(/\D/g, '');
            if (numbers.length <= 14) {
                return numbers
                    .replace(/(\d{2})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1/$2')
                    .replace(/(\d{4})(\d)/, '$1-$2');
            }
            return value;
        }

        // Aplicar máscara de CNPJ
        document.getElementById('tokenCnpj').addEventListener('input', function(e) {
            e.target.value = formatCNPJ(e.target.value);
        });

        // Submissão do formulário
        document.getElementById('tokenForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Gerando...';

            try {
                const response = await fetch('{{ route("dev.api-tokens.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Exibir token gerado
                    document.getElementById('generatedToken').value = data.token;
                    document.getElementById('generatedTokenContainer').classList.remove('hidden');
                    
                    // Limpar formulário
                    this.reset();
                    
                    // Recarregar página para atualizar lista
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Erro ao gerar token: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                alert('Erro ao gerar token. Por favor, tente novamente.');
                console.error(error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Copiar token
        document.getElementById('copyTokenBtn').addEventListener('click', function() {
            const tokenInput = document.getElementById('generatedToken');
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            
            try {
                navigator.clipboard.writeText(tokenInput.value).then(() => {
                    const btn = document.getElementById('copyTokenBtn');
                    const originalText = btn.textContent;
                    btn.textContent = 'Copiado!';
                    btn.classList.add('bg-[#10b981]');
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.remove('bg-[#10b981]');
                    }, 2000);
                });
            } catch (err) {
                alert('Erro ao copiar token. Por favor, copie manualmente.');
            }
        });

        // Toggle token active/inactive
        async function toggleToken(id) {
            if (!confirm('Tem certeza que deseja alterar o status deste token?')) {
                return;
            }

            try {
                const response = await fetch(`/dev/api-tokens/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erro ao alterar status do token: ' + (data.message || 'Erro desconhecido'));
                }
            } catch (error) {
                alert('Erro ao alterar status do token. Por favor, tente novamente.');
                console.error(error);
            }
        }
    </script>
</x-app-layout>
