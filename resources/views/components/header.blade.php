@php
    $userRole = 'medico'; // TODO: Implementar lógica de roles
    $roleLabels = [
        'medico' => 'Médico',
        'tecnico' => 'Técnico',
        'admin' => 'Administrador',
        'dev' => 'Desenvolvedor'
    ];
@endphp

<header class="h-16 bg-card border-b border-border flex items-center justify-between px-6">
    <!-- Barra de busca global -->
    <div class="flex-1 max-w-xl">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                placeholder="Buscar pacientes, exames..."
                class="w-full pl-10 pr-4 py-2 bg-input-background rounded-lg border border-border focus:outline-none focus:ring-2 focus:ring-primary"
            />
        </div>
    </div>

    <!-- Notificações e perfil -->
    <div class="flex items-center gap-4 ml-6">
        <!-- Notificações -->
        <button class="relative p-2 hover:bg-accent rounded-lg transition-colors">
            <svg class="w-5 h-5 text-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-destructive rounded-full"></span>
        </button>

        <!-- Perfil do usuário -->
        <div class="flex items-center gap-3 pl-4 border-l border-border">
            <div class="text-right">
                <p class="text-sm font-medium text-foreground">{{ Auth::user()->name }}</p>
                <p class="text-xs text-muted-foreground">{{ $roleLabels[$userRole] ?? 'Usuário' }}</p>
            </div>
            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </div>
</header>

