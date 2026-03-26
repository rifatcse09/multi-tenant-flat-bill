import Chart from 'chart.js/auto';

const brand = {
    primary: '#0d9488',
    light: 'rgba(13, 148, 136, 0.12)',
    slate: '#64748b',
};

function parseData(el, key, fallback = []) {
    if (!el?.dataset?.[key]) {
        return fallback;
    }
    try {
        return JSON.parse(el.dataset[key]);
    } catch {
        return fallback;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('adminDashboardAnalytics');
    if (!root) {
        return;
    }

    const payLabels = parseData(root, 'paymentLabels');
    const payValues = parseData(root, 'paymentValues');
    const planLabels = parseData(root, 'planLabels');
    const planValues = parseData(root, 'planValues');
    const statusLabels = parseData(root, 'statusLabels');
    const statusValues = parseData(root, 'statusValues');
    const plLabels = parseData(root, 'plLabels');
    const plValues = parseData(root, 'plValues');

    const commonHeight = { maintainAspectRatio: false };

    const elPay = document.getElementById('chartAdminPayments');
    if (elPay && payLabels.length) {
        new Chart(elPay, {
            type: 'line',
            data: {
                labels: payLabels,
                datasets: [
                    {
                        label: 'Tenant payments collected',
                        data: payValues,
                        borderColor: brand.primary,
                        backgroundColor: brand.light,
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: {
                ...commonHeight,
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                ` ${ctx.dataset.label}: $${Number(ctx.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => '$' + Number(v).toLocaleString(),
                        },
                    },
                },
            },
        });
    }

    const elPlan = document.getElementById('chartAdminPlans');
    if (elPlan && planLabels.length) {
        new Chart(elPlan, {
            type: 'doughnut',
            data: {
                labels: planLabels,
                datasets: [
                    {
                        data: planValues,
                        backgroundColor: ['#0d9488', '#14b8a6', '#2dd4bf', '#5eead4', '#99f6e4', '#ccfbf1'],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                ...commonHeight,
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }

    const elStatus = document.getElementById('chartAdminStatus');
    if (elStatus && statusLabels.length) {
        new Chart(elStatus, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [
                    {
                        label: 'Owners',
                        data: statusValues,
                        backgroundColor: 'rgba(13, 148, 136, 0.65)',
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                ...commonHeight,
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                },
            },
        });
    }

    const elPl = document.getElementById('chartAdminProfitLoss');
    if (elPl && plLabels.length === plValues.length && plLabels.length > 0) {
        const plColors = plLabels.map((label, i) => {
            if (i === 0) {
                return 'rgba(13, 148, 136, 0.75)';
            }
            if (i === 1) {
                return 'rgba(245, 158, 11, 0.7)';
            }
            const v = plValues[i];
            return v >= 0 ? 'rgba(16, 185, 129, 0.75)' : 'rgba(220, 38, 38, 0.6)';
        });
        new Chart(elPl, {
            type: 'bar',
            data: {
                labels: plLabels,
                datasets: [
                    {
                        label: 'Amount (USD)',
                        data: plValues,
                        backgroundColor: plColors,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                ...commonHeight,
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                ` $${Number(ctx.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
                        },
                    },
                },
                scales: {
                    y: {
                        ticks: {
                            callback: (v) => '$' + Number(v).toLocaleString(),
                        },
                    },
                },
            },
        });
    }
});
