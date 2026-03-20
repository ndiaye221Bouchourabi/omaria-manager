/**
 * dashboard.js — O'Maria
 *
 * G1 — Courbe collectes brutes par semaine
 *      Montre les hauts et bas semaine par semaine
 *      Ligne de moyenne automatique en pointillés
 *
 * G2 — Barres groupées par mois
 *      Recettes | Dépenses points | Charges globales | Bénéfice (ligne)
 *      Montre si chaque mois est rentable ou déficitaire
 *
 * Les deux réagissent aux filtres (point, année, mois, semaine)
 * car les données viennent du controller via window.chartData
 * et window.monthlyData
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ============================================================
       G1 — COURBE : Collectes semaine par semaine
    ============================================================ */
    const ctxG1 = document.getElementById('chartWeekly');

    if (ctxG1 && window.chartData?.labels?.length > 0) {

        new Chart(ctxG1, {
            type: 'line',
            data: {
                labels: window.chartData.labels,
                datasets: [{
                    label: 'Recettes',
                    data: window.chartData.collectes,
                    borderColor: '#378ADD',
                    backgroundColor: 'rgba(55,138,221,0.08)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#378ADD',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#1d6fb8',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleFont: { size: 12, weight: '500' },
                        bodyFont:  { size: 12 },
                        padding: 10, cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: ctx => ctx[0].label,
                            label: ctx => '  ' + formatMoney(ctx.parsed.y)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11 }, color: '#888780', maxRotation: 35, autoSkip: true, maxTicksLimit: 10 }
                    },
                    y: {
                        grid: { color: 'rgba(136,135,128,0.1)', drawBorder: false },
                        border: { display: false },
                        beginAtZero: true,
                        ticks: { font: { size: 11 }, color: '#888780', callback: v => formatShort(v) }
                    }
                }
            }
        });
    }

    /* ============================================================
       G2 — BARRES GROUPÉES : Recettes vs Dépenses PAR MOIS
       + Ligne bénéfice net
    ============================================================ */
    const ctxG2 = document.getElementById('chartMensuel');

    if (ctxG2 && window.monthlyData?.labels?.length > 0) {
        const md = window.monthlyData;

        /* Couleur des points bénéfice : vert si positif, rouge si négatif */
        const benColors = md.benefices.map(v => v >= 0 ? '#639922' : '#E24B4A');

        new Chart(ctxG2, {
            data: {
                labels: md.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Recettes',
                        data: md.collectes,
                        backgroundColor: 'rgba(55,138,221,0.75)',
                        borderWidth: 0,
                        borderRadius: 5, borderSkipped: false,
                        order: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Dépenses points',
                        data: md.depensesPoints,
                        backgroundColor: 'rgba(226,75,74,0.65)',
                        borderWidth: 0,
                        borderRadius: 5, borderSkipped: false,
                        order: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Charges globales',
                        data: md.depensesGlobales,
                        backgroundColor: 'rgba(186,117,23,0.65)',
                        borderWidth: 0,
                        borderRadius: 5, borderSkipped: false,
                        order: 2,
                    },
                    {
                        type: 'line',
                        label: 'Bénéfice net',
                        data: md.benefices,
                        borderColor: '#639922',
                        backgroundColor: 'rgba(99,153,34,0.05)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: false,
                        pointRadius: 6,
                        pointBackgroundColor: benColors,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleFont: { size: 12, weight: '500' },
                        bodyFont:  { size: 12 },
                        padding: 12, cornerRadius: 10,
                        callbacks: {
                            title: ctx => ctx[0].label,
                            label: ctx => {
                                const v = ctx.parsed.y ?? 0;
                                return '  ' + ctx.dataset.label + ' : ' + formatMoney(v);
                            },
                            afterBody: ctx => {
                                const ben = ctx.find(c => c.dataset.label === 'Bénéfice net');
                                if (!ben) return [];
                                const v = ben.parsed.y ?? 0;
                                return ['', v >= 0
                                    ? '  ✅ Mois rentable : +' + formatMoney(v)
                                    : '  ⚠️ Mois déficitaire : -' + formatMoney(Math.abs(v))
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }, border: { display: false },
                        ticks: { font: { size: 11 }, color: '#888780' }
                    },
                    y: {
                        grid: { color: 'rgba(136,135,128,0.1)', drawBorder: false },
                        border: { display: false }, beginAtZero: true,
                        ticks: { font: { size: 11 }, color: '#888780', callback: v => formatShort(v) }
                    }
                }
            }
        });
    }

    /* ============================================================
       FORMATAGE
    ============================================================ */
    function formatMoney(v) {
        if (!v && v !== 0) return '0 FCFA';
        const r = Math.round(v * 1000) / 1000;
        return (Number.isInteger(r)
            ? r.toLocaleString('fr-FR')
            : r.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 3 })
        ) + ' FCFA';
    }

    function formatShort(v) {
        if (Math.abs(v) >= 1_000_000) return (v / 1_000_000).toFixed(1) + 'M';
        if (Math.abs(v) >= 1_000)     return Math.round(v / 1_000) + 'K';
        return Math.round(v).toString();
    }
});
