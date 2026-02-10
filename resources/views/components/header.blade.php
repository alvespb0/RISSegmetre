@php
    $userRole = Auth::user()->tipo;
    $roleLabels = [
        'medico' => 'Médico',
        'tecnico' => 'Técnico',
        'admin' => 'Administrador',
        'dev' => 'Desenvolvedor'
    ];
@endphp

<header class="h-16 bg-card border-b border-border flex items-center justify-between px-6">
    <!-- Barra de busca global -->
    <div class="flex-1">
        <div class="flex flex-col justify-center h-full">
            <h1 class="text-lg font-bold text-foreground tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Segmetre Ambiental Assessoria LTDA
            </h1>
            <p class="text-xs text-muted-foreground font-medium pl-7">
                Sistema Integrado de Radiologia e Imagem (RIS)
            </p>
        </div>
    </div>
    <!-- Notificações e perfil -->
    <div class="flex items-center gap-4 ml-6">
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

