@php
    $currentRoute = request()->route()->getName() ?? 'dashboard';
@endphp

<aside class="w-64 h-screen bg-sidebar border-r border-sidebar-border flex flex-col">
    <div class="p-6">
        <h1 class="text-xl font-semibold text-primary">SEGMETRE</h1>
        <p class="text-sm text-muted-foreground mt-1">Radiology Information System</p>
    </div>

    <nav class="flex-1 px-3 overflow-y-auto">
        <!-- Dashboard -->
        @php
            $isDashboardActive = request()->routeIs('dashboard');
            $dashboardClasses = $isDashboardActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('dashboard') }}" class="{{ $dashboardClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Lista de Exames -->
        @php
            $isExamesActive = request()->routeIs('exames.*');
            $examesClasses = $isExamesActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('exames.index') }}" class="{{ $examesClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Lista de Exames</span>
        </a>

        <!-- Pacientes -->
        @php
            $isPacientesActive = request()->routeIs('pacientes.*');
            $pacientesClasses = $isPacientesActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('pacientes.index') }}" class="{{ $pacientesClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87m8-6.13a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Pacientes</span>
        </a>

        <!-- Relatórios -->
        @php
            $isRelatoriosActive = request()->routeIs('relatorios.*');
            $relatoriosClasses = $isRelatoriosActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('relatorios.index') }}" class="{{ $relatoriosClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>Relatórios</span>
        </a>

        <!-- Usuários -->
        @php
            $isUsuariosActive = request()->routeIs('usuarios.*');
            $usuariosClasses = $isUsuariosActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('usuarios.index') }}" class="{{ $usuariosClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Usuários</span>
        </a>

        <!-- Configurações -->
        @php
            $isProfileActive = request()->routeIs('profile.*');
            $profileClasses = $isProfileActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('profile.edit') }}" class="{{ $profileClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14c3.866 0 7 1.79 7 4v2H5v-2c0-2.21 3.134-4 7-4zm0-2a4 4 0 100-8 4 4 0 000 8z"/>
            </svg>
            <span>Perfil</span>
        </a>

        @if(Auth::check() && Auth::user()->tipo === 'dev')
        <!-- API Tokens (Apenas para Desenvolvedores) -->
        @php
            $isApiTokensActive = request()->routeIs('dev.api-tokens*');
            $apiTokensClasses = $isApiTokensActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('dev.api-tokens') }}" class="{{ $apiTokensClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            <span>API Tokens</span>
        </a>

        <!-- Integrações -->
        @php
            $isIntegracoesActive = request()->routeIs('dev.integracoes*');
            $integracoesClasses = $isIntegracoesActive 
                ? 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors bg-sidebar-accent text-sidebar-accent-foreground'
                : 'w-full flex items-center gap-3 px-4 py-3 mb-1 rounded-lg transition-colors text-sidebar-foreground hover:bg-sidebar-accent/50';
        @endphp
        <a href="{{ route('dev.integracoes.index') }}" class="{{ $integracoesClasses }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span>Integrações</span>
        </a>
        @endif
    </nav>

    <!-- Botão de Logout -->
    <div class="p-3 border-t border-sidebar-border">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-sidebar-foreground hover:bg-destructive/10 hover:text-destructive"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Sair</span>
            </button>
        </form>
    </div>
</aside>

