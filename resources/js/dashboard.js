import Chart from 'chart.js/auto';

// Cores do tema
const themeColors = {
    primary: '#10b981',
    secondary: '#84cc16',
    destructive: '#ef4444',
    pending: '#eab308',
    urgent: '#f97316',
    muted: '#64748b',
    background: '#fafaf9',
    card: '#ffffff',
    border: '#cbd5e1',
};

// Função para criar gráfico de linha (Exames ao longo do tempo)
export function initExamesTempoChart() {
    const ctx = document.getElementById('examesTempoChart');
    if (!ctx) return;

    // Obter dados dos atributos data-*
    const labels = JSON.parse(ctx.getAttribute('data-labels') || '[]');
    const exames = JSON.parse(ctx.getAttribute('data-exames') || '[]');
    const laudos = JSON.parse(ctx.getAttribute('data-laudos') || '[]');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Exames Realizados',
                data: exames,
                borderColor: themeColors.primary,
                backgroundColor: themeColors.primary + '20',
                tension: 0.4,
                fill: true,
                animation: {
                    duration: 2500,
                    delay: (context) => context.dataIndex * 80,
                },
            }, {
                label: 'Laudos Concluídos',
                data: laudos,
                borderColor: themeColors.secondary,
                backgroundColor: themeColors.secondary + '20',
                tension: 0.4,
                fill: true,
                animation: {
                    duration: 2500,
                    delay: (context) => context.dataIndex * 80 + 200,
                },
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2500,
                easing: 'easeInOutQuart',
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default') {
                        delay = context.dataIndex * 100 + context.datasetIndex * 50;
                    }
                    return delay;
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#475569',
                        usePointStyle: true,
                        padding: 15,
                    }
                },
                tooltip: {
                    backgroundColor: themeColors.card,
                    titleColor: '#475569',
                    bodyColor: '#475569',
                    borderColor: themeColors.border,
                    borderWidth: 1,
                    padding: 12,
                }
            },
            scales: {
                x: {
                    grid: {
                        color: themeColors.border + '40',
                    },
                    ticks: {
                        color: themeColors.muted,
                    }
                },
                y: {
                    grid: {
                        color: themeColors.border + '40',
                    },
                    ticks: {
                        color: themeColors.muted,
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

// Função para criar gráfico de pizza (Exames por Status)
export function initExamesStatusChart() {
    const ctx = document.getElementById('examesStatusChart');
    if (!ctx) return;

    // Obter dados dos atributos data-*
    const labels = JSON.parse(ctx.getAttribute('data-labels') || '[]');
    const values = JSON.parse(ctx.getAttribute('data-values') || '[]');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    themeColors.primary,
                    themeColors.pending,
                    themeColors.urgent,
                    themeColors.destructive,
                ],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 3000,
                easing: 'easeOutQuart',
                delay: (context) => {
                    if (context.type === 'data') {
                        return context.dataIndex * 150;
                    }
                    return 0;
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#475569',
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                        }
                    }
                },
                tooltip: {
                    backgroundColor: themeColors.card,
                    titleColor: '#475569',
                    bodyColor: '#475569',
                    borderColor: themeColors.border,
                    borderWidth: 1,
                    padding: 12,
                }
            }
        }
    });
}

// Função para criar gráfico de barras (Exames por Modalidade)
export function initExamesModalidadeChart() {
    const ctx = document.getElementById('examesModalidadeChart');
    if (!ctx) return;

    // Obter dados dos atributos data-*
    const labels = JSON.parse(ctx.getAttribute('data-labels') || '[]');
    const values = JSON.parse(ctx.getAttribute('data-values') || '[]');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantidade de Exames',
                data: values,
                backgroundColor: themeColors.primary + '80',
                borderColor: themeColors.primary,
                borderWidth: 2,
                borderRadius: 6,
                animation: {
                    duration: 2000,
                    delay: (context) => {
                        return context.dataIndex * 120;
                    },
                },
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2500,
                easing: 'easeInOutQuart',
                delay: (context) => {
                    if (context.type === 'data') {
                        return context.dataIndex * 100;
                    }
                    return 0;
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: themeColors.card,
                    titleColor: '#475569',
                    bodyColor: '#475569',
                    borderColor: themeColors.border,
                    borderWidth: 1,
                    padding: 12,
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: themeColors.muted,
                        font: {
                            size: 11,
                        }
                    }
                },
                y: {
                    grid: {
                        color: themeColors.border + '40',
                    },
                    ticks: {
                        color: themeColors.muted,
                    },
                    beginAtZero: true
                }
            }
        }
    });
}

// Função para criar gráfico de área (Performance de Laudos)
export function initPerformanceLaudosChart() {
    const ctx = document.getElementById('performanceLaudosChart');
    if (!ctx) return;

    // Obter dados dos atributos data-*
    const labels = JSON.parse(ctx.getAttribute('data-labels') || '[]');
    const values = JSON.parse(ctx.getAttribute('data-values') || '[]');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tempo Médio (horas)',
                data: values,
                borderColor: themeColors.secondary,
                backgroundColor: themeColors.secondary + '30',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                animation: {
                    duration: 2500,
                    delay: (context) => context.dataIndex * 80,
                },
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2500,
                easing: 'easeInOutQuart',
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: themeColors.card,
                    titleColor: '#475569',
                    bodyColor: '#475569',
                    borderColor: themeColors.border,
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Tempo médio: ' + context.parsed.y + ' horas';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: themeColors.border + '40',
                    },
                    ticks: {
                        color: themeColors.muted,
                    }
                },
                y: {
                    grid: {
                        color: themeColors.border + '40',
                    },
                    ticks: {
                        color: themeColors.muted,
                        callback: function(value) {
                            return value + 'h';
                        }
                    },
                    beginAtZero: false
                }
            }
        }
    });
}

// Inicializar todos os gráficos com delay entre eles para efeito cascata
export function initDashboardCharts() {
    // Criar gráficos com pequenos delays para efeito cascata
    setTimeout(() => initExamesTempoChart(), 100);
    setTimeout(() => initExamesStatusChart(), 300);
    setTimeout(() => initExamesModalidadeChart(), 500);
    setTimeout(() => initPerformanceLaudosChart(), 700);
}

// Exportar função para inicialização manual
// A inicialização será feita diretamente na view da dashboard

