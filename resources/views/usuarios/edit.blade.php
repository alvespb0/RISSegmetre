<x-app-layout>
    <div class="p-6 flex flex-col items-center min-h-full">
        <div class="w-full max-w-4xl mb-6 text-center">
            <h2 class="text-2xl font-semibold text-foreground mb-2">Editar Usuário</h2>
            <p class="text-muted-foreground">
                Preencha os dados abaixo para editar o usuário
            </p>
        </div>

        <div 
            x-data="{
                tipo: '{{ old('tipo', $user->tipo) }}',
                alterarSenha: false
            }"
            class="w-full max-w-4xl bg-card border border-border rounded-lg shadow-sm overflow-hidden"
        >
            <form method="POST" action="{{route('usuarios.update', $user->id)}}" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nome --}}
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="name" value="Insira o nome do usuário" />
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="block mt-1 w-full" 
                            value="{{ $user->name }}" 
                            required 
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="block mt-1 w-full" 
                            value="{{ $user->email }}" 
                            required 
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Tipo --}}
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

                    {{-- Dados do Médico --}}
                    <div 
                        x-show="tipo === 'medico'"
                        x-transition.opacity
                        x-cloak
                        class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-sidebar-accent/20 rounded-lg border border-border"
                    >
                        <div>
                            <x-input-label for="especialidade" value="Especialidade" />
                            <x-text-input 
                                id="especialidade" 
                                name="especialidade" 
                                type="text" 
                                class="block mt-1 w-full" 
                                value="{{ $user->medico?->especialidade }}" 
                            />
                        </div>

                        <div>
                            <x-input-label for="conselho_classe" value="Conselho de Classe (CRM)" />
                            <x-text-input 
                                id="conselho_classe" 
                                name="conselho_classe" 
                                type="text" 
                                class="block mt-1 w-full" 
                                value="{{ $user->medico?->conselho_classe }}" 
                            />
                        </div>
                    </div>

                    {{-- Botão alterar senha --}}
                    <div class="col-span-1 md:col-span-2">
                        <button
                            type="button"
                            @click="alterarSenha = !alterarSenha"
                            class="text-sm text-primary hover:underline"
                        >
                            <span x-show="!alterarSenha">🔒 Alterar senha</span>
                            <span x-show="alterarSenha">❌ Cancelar alteração de senha</span>
                        </button>
                    </div>

                    {{-- Bloco de senha --}}
                    <div 
                        x-show="alterarSenha"
                        x-transition.opacity
                        x-cloak
                        class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-sidebar-accent/20 rounded-lg border border-border"
                    >
                        <div>
                            <x-input-label for="password" value="Nova senha" />
                            <x-text-input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="block mt-1 w-full" 
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar nova senha" />
                            <x-text-input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                class="block mt-1 w-full" 
                            />
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-center gap-4 pt-6 border-t border-border">
                    <a 
                        href="{{ route('usuarios.index') }}" 
                        class="text-sm text-muted-foreground hover:text-foreground transition-colors"
                    >
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
