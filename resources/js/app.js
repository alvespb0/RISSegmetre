import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
