<x-app-layout>
    {{-- Container principal com flex para centralização horizontal --}}
    <div class="p-6 flex flex-col items-center min-h-full">
        
        {{-- Cabeçalho alinhado ao centro e com largura limitada compatível com o form --}}
        <div class="w-full max-w-4xl mb-6 text-center">
            <h2 class="text-2xl font-semibold text-foreground mb-2">Cadastrar Novo Usuário</h2>
            <p class="text-muted-foreground">Preencha os dados abaixo para criar um novo acesso ao sistema</p>
        </div>

        <div 
            x-data="{ tipo: '{{ old('tipo') }}' }" 
            class="w-full max-w-4xl bg-card border border-border rounded-lg shadow-sm overflow-hidden"
        >
            <form method="POST" action="{{ route('register') }}" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="name" value="Nome completo" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo de usuário" />
                        <select
                            id="tipo"
                            name="tipo"
                            x-model="tipo"
                            required
                            class="block mt-1 w-full border-border bg-background text-foreground focus:border-primary focus:ring-primary rounded-md shadow-sm"
                        >
                            <option value="">Selecione o tipo</option>
                            <option value="admin">Administrador</option>
                            <option value="medico">Médico</option>
                            <option value="tecnico">Técnico</option>
                            <option value="dev">Desenvolvedor</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                    </div>

                    <div 
                        x-show="tipo === 'medico'" 
                        x-transition.opacity
                        x-cloak
                        class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-sidebar-accent/20 rounded-lg border border-border"
                    >
                        <div>
                            <x-input-label for="especialidade" value="Especialidade" />
                            <x-text-input id="especialidade" name="especialidade" type="text" class="block mt-1 w-full" :value="old('especialidade')" />
                        </div>
                        <div>
                            <x-input-label for="conselho_classe" value="Conselho de Classe (CRM)" />
                            <x-text-input id="conselho_classe" name="conselho_classe" type="text" class="block mt-1 w-full" :value="old('conselho_classe')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="password" value="Senha temporária" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar senha" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required />
                    </div>
                </div>

                <div class="flex items-center justify-center gap-4 pt-6 border-t border-border">
                    <a href="{{ route('usuarios.index') }}" class="text-sm text-muted-foreground hover:text-foreground transition-colors">
                        Cancelar e voltar
                    </a>
                    <x-primary-button>
                        {{ __('Salvar Usuário') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>