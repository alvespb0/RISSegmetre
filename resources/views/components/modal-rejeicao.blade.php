<div 
    x-show="modalRejeicaoOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <!-- Backdrop -->
    <div 
        @click="fecharModal()"
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
    ></div>

    <!-- Modal -->
    <div 
        class="relative bg-card border border-border rounded-lg shadow-lg w-full max-w-md mx-4 p-6"
        @click.stop
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-foreground">Rejeitar Exame</h3>
            <button
                @click="fecharModal()"
                class="p-1 hover:bg-accent rounded-lg transition-colors"
            >
                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="mb-6">
            <label for="justificativa" class="block text-sm font-medium text-foreground mb-2">
                Justificativa Técnica <span class="text-destructive">*</span>
            </label>
            <textarea
                id="justificativa"
                x-model="justificativa"
                placeholder="Descreva o motivo da rejeição do exame (ex: qualidade inadequada, posicionamento incorreto, artefatos...)"
                class="w-full px-4 py-3 bg-input-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary min-h-[120px] resize-y"
                rows="5"
            ></textarea>
            <p class="text-xs text-muted-foreground mt-2">
                Esta justificativa será registrada no sistema e enviada ao técnico responsável.
            </p>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 justify-end">
            <button
                @click="fecharModal()"
                class="px-4 py-2 bg-card border border-border text-foreground rounded-lg hover:bg-accent transition-colors"
            >
                Cancelar
            </button>
            <button
                @click="confirmarRejeicao()"
                :disabled="!justificativa.trim()"
                class="px-4 py-2 bg-destructive text-destructive-foreground rounded-lg hover:bg-destructive/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Confirmar Rejeição
            </button>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

