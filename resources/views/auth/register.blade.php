<x-guest-layout>
    <div 
        x-data="{ tipo: '{{ old('tipo') }}' }"
        class="mb-6"
    >
        <h2 class="text-2xl font-semibold text-foreground mb-2">Criar nova conta</h2>
        <p class="text-sm text-muted-foreground">Preencha os dados abaixo para se registrar</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Nome -->
            <div>
                <x-input-label for="name" value="Nome completo" />
                <x-text-input id="name" name="name" type="text" required />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" required />
            </div>

            <!-- Senha -->
            <div>
                <x-input-label for="password" value="Senha" />
                <x-text-input id="password" name="password" type="password" required />
            </div>

            <!-- Confirmar senha -->
            <div>
                <x-input-label for="password_confirmation" value="Confirmar senha" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required />
            </div>

            <!-- Tipo -->
            <div>
                <x-input-label for="tipo" value="Tipo de usuário" />
                <select
                    id="tipo"
                    name="tipo"
                    x-model="tipo"
                    required
                    class="w-full px-4 py-2.5 bg-input-background border border-border rounded-lg"
                >
                    <option value="">Selecione o tipo</option>
                    <option value="admin">Administrador</option>
                    <option value="medico">Médico</option>
                    <option value="tecnico">Técnico</option>
                    <option value="dev">Desenvolvedor</option>
                </select>
            </div>

            <!-- Campos extras do médico -->
            <div
                x-show="tipo === 'medico'"
                x-transition
                x-cloak
                class="space-y-5"
            >
                <div>
                    <x-input-label for="especialidade" value="Especialidade" />
                    <x-text-input id="especialidade" name="especialidade" type="text" />
                </div>

                <div>
                    <x-input-label for="conselho_classe" value="Conselho de Classe (CRM)" />
                    <x-text-input id="conselho_classe" name="conselho_classe" type="text" />
                </div>
            </div>

            <x-primary-button class="w-full">
                Criar conta
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
