/**
 * dashboard.js — O'Maria
 *
 * G1 — Courbe collectes brutes par semaine
 * G2 — Barres groupées par mois
 *      Recettes | Dépenses points | Charges globales | Bénéfice net (BAR)
 *
 * FIX : bénéfice net passe de type 'line' → 'bar' (barres vertes/rouges)
 * RESPONSIVE : maintainAspectRatio: false + media queries injectées
 */

document.addEventListener("DOMContentLoaded", function () {
    /* ============================================================
       FORMATAGE
    ============================================================ */
    function formatMoney(v) {
        if (!v && v !== 0) return "0 FCFA";
        const r = Math.round(v * 1000) / 1000;
        return (
            (Number.isInteger(r)
                ? r.toLocaleString("fr-FR")
                : r.toLocaleString("fr-FR", {
                      minimumFractionDigits: 0,
                      maximumFractionDigits: 3,
                  })) + " FCFA"
        );
    }

    function formatShort(v) {
        if (Math.abs(v) >= 1_000_000) return (v / 1_000_000).toFixed(1) + "M";
        if (Math.abs(v) >= 1_000) return Math.round(v / 1_000) + "K";
        return Math.round(v).toString();
    }

    /* ============================================================
       STYLES COMMUNS
    ============================================================ */
    const tooltipBase = {
        backgroundColor: "rgba(15,23,42,0.92)",
        titleFont: { size: 12, weight: "500" },
        bodyFont: { size: 12 },
        padding: 12,
        cornerRadius: 10,
    };

    const scaleX = {
        grid: { display: false },
        border: { display: false },
        ticks: {
            font: { size: 11 },
            color: "#888780",
            maxRotation: 35,
            autoSkip: true,
            maxTicksLimit: 10,
        },
    };

    const scaleY = {
        grid: { color: "rgba(136,135,128,0.1)", drawBorder: false },
        border: { display: false },
        beginAtZero: true,
        ticks: {
            font: { size: 11 },
            color: "#888780",
            callback: (v) => formatShort(v),
        },
    };

    /* ============================================================
       G1 — COURBE : Collectes semaine par semaine
    ============================================================ */
    const ctxG1 = document.getElementById("chartWeekly");

    if (ctxG1 && window.chartData?.labels?.length > 0) {
        new Chart(ctxG1, {
            type: "line",
            data: {
                labels: window.chartData.labels,
                datasets: [
                    {
                        label: "Recettes",
                        data: window.chartData.collectes,
                        borderColor: "#378ADD",
                        backgroundColor: "rgba(55,138,221,0.08)",
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointBackgroundColor: "#378ADD",
                        pointBorderColor: "#fff",
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: "#1d6fb8",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipBase,
                        displayColors: false,
                        callbacks: {
                            title: (ctx) => ctx[0].label,
                            label: (ctx) => "  " + formatMoney(ctx.parsed.y),
                        },
                    },
                },
                scales: { x: scaleX, y: scaleY },
            },
        });
    }

    /* ============================================================
       G2 — BARRES GROUPÉES : Recettes vs Dépenses PAR MOIS
       ──────────────────────────────────────────────────────────
       CORRECTION : Bénéfice net était type 'line' → maintenant 'bar'
       Barre verte  si bénéfice >= 0
       Barre rouge  si bénéfice <  0 (mois déficitaire)
    ============================================================ */
    const ctxG2 = document.getElementById("chartMensuel");

    if (ctxG2 && window.monthlyData?.labels?.length > 0) {
        const md = window.monthlyData;

        /* Couleur dynamique barre par barre selon le signe */
        const benBg = md.benefices.map((v) =>
            v >= 0 ? "rgba(99,153,34,0.82)" : "rgba(226,75,74,0.82)",
        );
        const benBorder = md.benefices.map((v) =>
            v >= 0 ? "#3B6D11" : "#A32D2D",
        );

        new Chart(ctxG2, {
            type: "bar",
            data: {
                labels: md.labels,
                datasets: [
                    {
                        label: "Recettes",
                        data: md.collectes,
                        backgroundColor: "rgba(55,138,221,0.75)",
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: "bottom",
                        order: 1,
                    },
                    {
                        label: "Dépenses points",
                        data: md.depensesPoints,
                        backgroundColor: "rgba(226,75,74,0.65)",
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: "bottom",
                        order: 2,
                    },
                    {
                        label: "Charges globales",
                        data: md.depensesGlobales,
                        backgroundColor: "rgba(186,117,23,0.65)",
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: "bottom",
                        order: 3,
                    },
                    {
                        /* ── CORRECTION PRINCIPALE ──
                           Ancien code : type: 'line', borderColor: '#639922', tension: 0.35, fill: false
                           Nouveau     : type: 'bar', couleur dynamique vert/rouge selon signe
                        ── */
                        label: "Bénéfice net",
                        data: md.benefices,
                        type: "bar",
                        backgroundColor: benBg,
                        borderColor: benBorder,
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: "bottom",
                        order: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: "index", intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipBase,
                        callbacks: {
                            title: (ctx) => ctx[0].label,
                            label: (ctx) => {
                                const v = ctx.parsed.y ?? 0;
                                return (
                                    "  " +
                                    ctx.dataset.label +
                                    " : " +
                                    formatMoney(v)
                                );
                            },
                            afterBody: (ctx) => {
                                const ben = ctx.find(
                                    (c) => c.dataset.label === "Bénéfice net",
                                );
                                if (!ben) return [];
                                const v = ben.parsed.y ?? 0;
                                return [
                                    "",
                                    v >= 0
                                        ? "  ✅ Mois rentable : +" +
                                          formatMoney(v)
                                        : "  ⚠️ Mois déficitaire : -" +
                                          formatMoney(Math.abs(v)),
                                ];
                            },
                        },
                    },
                },
                scales: {
                    x: scaleX,
                    y: {
                        ...scaleY,
                        /* L'axe peut descendre sous zéro pour les mois déficitaires */
                        beginAtZero: false,
                        ticks: {
                            font: { size: 11 },
                            color: "#888780",
                            callback: (v) => {
                                if (v < 0)
                                    return "−" + formatShort(Math.abs(v));
                                return formatShort(v);
                            },
                        },
                    },
                },
            },
        });
    }

    /* ============================================================
       RESPONSIVE — CSS injecté dynamiquement
       Couvre : KPI grid, main-grid, filtres, graphiques, sidebar
    ============================================================ */
    const css = document.createElement("style");
    css.textContent = `

        /* ── Layout racine ── */
        .dash-wrap {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* ── KPI : 4 colonnes par défaut ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        /* ── Zone principale : graphiques + sidebar ── */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px;
            align-items: start;
        }

        .charts-left {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-width: 0;   /* évite le dépassement du flex child */
        }

        /* ── Hauteurs des canvas ── */
        .chart-container-g1 { position: relative; height: 260px; }
        .chart-container-g2 { position: relative; height: 280px; }

        .chart-container-g1 canvas,
        .chart-container-g2 canvas {
            width:  100% !important;
            height: 100% !important;
        }

        /* ─────────────────────────────────────────────
           TABLETTE large (≤ 1100px)
           Sidebar passe sous les graphiques
        ───────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            .right-col {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
        }

        /* ─────────────────────────────────────────────
           TABLETTE (≤ 768px)
           KPI : 2 colonnes
           Filtres : 2 colonnes
        ───────────────────────────────────────────── */
        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }
            .right-col {
                grid-template-columns: 1fr;
            }
            .chart-container-g1 { height: 220px; }
            .chart-container-g2 { height: 240px; }

            /* Filtres Bootstrap : forcer 2 col */
            #filterForm .row > [class*="col-"] {
                flex: 0 0 50%;
                max-width: 50%;
            }
            /* Bouton Filtrer : pleine largeur */
            #filterForm .row > .col-lg-2:last-child {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Légende graphique : wrappable */
            .chart-legend {
                flex-wrap: wrap;
                row-gap: 4px;
            }
        }

        /* ─────────────────────────────────────────────
           MOBILE portrait (≤ 480px)
           KPI : 2 colonnes compactes
           Filtres : 1 colonne
           Graphiques : hauteur réduite
        ───────────────────────────────────────────── */
        @media (max-width: 480px) {
            .kpi-grid {
                gap: 8px;
            }
            .kpi-card {
                padding: 12px 12px !important;
            }
            .kpi-value {
                font-size: 1rem !important;
                word-break: break-all;
            }
            .kpi-title {
                font-size: 10px !important;
            }
            .kpi-icon {
                display: none;
            }

            .chart-container-g1 { height: 185px; }
            .chart-container-g2 { height: 200px; }

            /* Filtres : 1 colonne */
            #filterForm .row > [class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Section label */
            .section-label {
                font-size: 11px;
            }

            /* Top5 */
            .top5-amount {
                font-size: 11px !important;
                min-width: unset !important;
            }
            .top5-name {
                font-size: 11px !important;
            }
            .top5-bar-wrap {
                display: none; /* masquer la barre sur très petit écran */
            }

            /* Alertes */
            .alerts-card {
                font-size: 12px;
            }
            .alert-row {
                padding: 6px 10px !important;
            }
        }

        /* ─────────────────────────────────────────────
           TRÈS PETIT (≤ 360px)
        ───────────────────────────────────────────── */
        @media (max-width: 360px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
            .chart-container-g1 { height: 160px; }
            .chart-container-g2 { height: 175px; }
        }
    `;
    document.head.appendChild(css);
});
