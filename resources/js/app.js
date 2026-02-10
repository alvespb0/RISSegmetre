import './bootstrap';

// Livewire 3 já inclui o Alpine.js automaticamente
// Não precisamos inicializar manualmente para evitar conflitos

// Carregar gráficos da dashboard se estiver na página
if (document.getElementById('examesTempoChart') || 
    document.getElementById('examesStatusChart') || 
    document.getElementById('examesModalidadeChart') || 
    document.getElementById('performanceLaudosChart')) {
    import('./dashboard.js').then(module => {
        setTimeout(() => {
            if (module.initDashboardCharts) {
                module.initDashboardCharts();
            }
        }, 300);
    });
}
