@php
    $mensagem = session('mensagem');
    $error = session('error');
    $hasNotifications = $mensagem || $error;
@endphp

@if($hasNotifications)
    <div class="fixed top-4 right-4 z-50 max-w-md w-full space-y-3">
        @if($mensagem)
            <div 
                x-data="{ 
                    show: true,
                    progress: 100
                }"
                x-init="
                    const duration = 3000;
                    const interval = 20;
                    const decrement = (100 / duration) * interval;
                    const timer = setInterval(() => {
                        progress -= decrement;
                        if (progress <= 0) {
                            clearInterval(timer);
                            show = false;
                        }
                    }, interval);
                    setTimeout(() => show = false, duration);
                "
                x-show="show"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2 scale-95"
                style="display: none;"
            >
                <div class="relative bg-gradient-to-r from-[#10b981] to-[#059669] border border-[#10b981]/20 rounded-xl shadow-2xl p-5 flex items-start gap-4 overflow-hidden backdrop-blur-sm">
                    <!-- Efeito de brilho -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-transparent to-transparent"></div>
                    
                    <!-- Ícone Sucesso com fundo -->
                    <div class="relative flex-shrink-0 w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Conteúdo -->
                    <div class="relative flex-1 min-w-0 pt-0.5">
                        <p class="text-sm font-semibold text-white leading-relaxed">
                            {{ $mensagem }}
                        </p>
                    </div>

                    <!-- Botão Fechar -->
                    <button
                        @click="show = false"
                        class="relative flex-shrink-0 w-8 h-8 rounded-lg transition-all duration-200 text-white hover:bg-white/25 active:scale-95 flex items-center justify-center"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Barra de progresso -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/10">
                        <div 
                            class="h-full bg-white/40 transition-all duration-20 ease-linear"
                            :style="`width: ${progress}%`"
                        ></div>
                    </div>
                </div>
            </div>
        @endif

        @if($error)
            <div 
                x-data="{ 
                    show: true,
                    progress: 100
                }"
                x-init="
                    const duration = 3000;
                    const interval = 20;
                    const decrement = (100 / duration) * interval;
                    const timer = setInterval(() => {
                        progress -= decrement;
                        if (progress <= 0) {
                            clearInterval(timer);
                            show = false;
                        }
                    }, interval);
                    setTimeout(() => show = false, duration);
                "
                x-show="show"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2 scale-95"
                style="display: none;"
            >
                <div class="relative bg-gradient-to-r from-destructive to-[#dc2626] border border-destructive/20 rounded-xl shadow-2xl p-5 flex items-start gap-4 overflow-hidden backdrop-blur-sm">
                    <!-- Efeito de brilho -->
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-transparent to-transparent"></div>
                    
                    <!-- Ícone Erro com fundo -->
                    <div class="relative flex-shrink-0 w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6 text-destructive-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Conteúdo -->
                    <div class="relative flex-1 min-w-0 pt-0.5">
                        <p class="text-sm font-semibold text-destructive-foreground leading-relaxed">
                            {{ $error }}
                        </p>
                    </div>

                    <!-- Botão Fechar -->
                    <button
                        @click="show = false"
                        class="relative flex-shrink-0 w-8 h-8 rounded-lg transition-all duration-200 text-destructive-foreground hover:bg-white/25 active:scale-95 flex items-center justify-center"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Barra de progresso -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/10">
                        <div 
                            class="h-full bg-white/40 transition-all duration-20 ease-linear"
                            :style="`width: ${progress}%`"
                        ></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

<style>
    [x-cloak] { display: none !important; }
</style>

