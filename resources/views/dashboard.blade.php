@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
    @php use App\Helpers\FinanceHelper; @endphp

    {{-- ================================================================
         ICÔNES SVG PREMIUM — injectées via un bloc <svg> masqué (sprite)
         Chaque symbol est réutilisable partout dans la page via <use>
    ================================================================ --}}
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">

        {{-- Goutte d'eau — Points / Machines --}}
        <symbol id="ico-drop" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2C6.5 8.5 4 12.5 4 15.5a8 8 0 0 0 16 0C20 12.5 17.5 8.5 12 2z"/>
        </symbol>

        {{-- Calendrier année — pastilles en bas --}}
        <symbol id="ico-cal-year" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
            <circle cx="8"  cy="15" r="1" fill="currentColor" stroke="none"/>
            <circle cx="12" cy="15" r="1" fill="currentColor" stroke="none"/>
            <circle cx="16" cy="15" r="1" fill="currentColor" stroke="none"/>
            <circle cx="8"  cy="19" r="1" fill="currentColor" stroke="none"/>
            <circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/>
            <circle cx="16" cy="19" r="1" fill="currentColor" stroke="none"/>
        </symbol>

        {{-- Calendrier mois — carré coloré --}}
        <symbol id="ico-cal-month" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
            <rect x="7" y="14" width="4" height="4" rx="1" fill="currentColor" stroke="none"/>
            <line x1="14" y1="15" x2="17" y2="15"/>
            <line x1="14" y1="18" x2="16" y2="18"/>
        </symbol>

        {{-- Calendrier semaine — lignes horizontales --}}
        <symbol id="ico-cal-week" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
            <line x1="7"  y1="14" x2="17" y2="14"/>
            <line x1="7"  y1="18" x2="14" y2="18"/>
        </symbol>

        {{-- Activité / période — onde --}}
        <symbol id="ico-activity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </symbol>

        {{-- Entonnoir — Filtrer --}}
        <symbol id="ico-filter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </symbol>

        {{-- Croix — Réinitialiser --}}
        <symbol id="ico-reset" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6"  y1="6" x2="18" y2="18"/>
        </symbol>

    </svg>

    <div class="dash-wrap">

    {{-- ============================================================
         FILTRES
         ============================================================ --}}
    <div class="filters-card">
        <form id="filterForm" action="{{ route('dashboard') }}" method="GET">
            <div class="row g-2 align-items-end">

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="filter-label">
                        <svg width="13" height="13" style="vertical-align:-2px;opacity:.7;"><use href="#ico-drop"/></svg>
                        Point
                    </div>
                    <div class="premium-select-wrap">
                        <svg width="14" height="14" class="pselect-icon"><use href="#ico-drop"/></svg>
                        <select name="point_id" class="premium-select" onchange="this.form.submit()">
                            <option value="">Toutes les machines</option>
                            @foreach($allPoints as $p)
                                <option value="{{ $p->id }}" {{ $pointId == $p->id ? 'selected' : '' }}>
                                    {{ $p->nom_machine }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="filter-label">
                        <svg width="13" height="13" style="vertical-align:-2px;opacity:.7;"><use href="#ico-cal-year"/></svg>
                        Année
                    </div>
                    <div class="premium-select-wrap">
                        <svg width="14" height="14" class="pselect-icon"><use href="#ico-cal-year"/></svg>
                        <select name="year" class="premium-select" onchange="this.form.submit()">
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="filter-label">
                        <svg width="13" height="13" style="vertical-align:-2px;opacity:.7;"><use href="#ico-cal-month"/></svg>
                        Mois
                    </div>
                    <div class="premium-select-wrap">
                        <svg width="14" height="14" class="pselect-icon"><use href="#ico-cal-month"/></svg>
                        <select name="month" class="premium-select" onchange="this.form.submit()">
                            <option value="">Tous les mois</option>
                            @foreach($availableMonths as $num => $nom)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="filter-label">
                        <svg width="13" height="13" style="vertical-align:-2px;opacity:.7;"><use href="#ico-cal-week"/></svg>
                        Semaine
                    </div>
                    <div class="premium-select-wrap">
                        <svg width="14" height="14" class="pselect-icon"><use href="#ico-cal-week"/></svg>
                        <select name="week" class="premium-select" onchange="this.form.submit()">
                            <option value="">Toutes</option>
                            @forelse($availableWeeks as $sem)
                                <option value="{{ $sem->week_number }}"
                                    {{ $selectedWeek == $sem->week_number ? 'selected' : '' }}>
                                    {{ $sem->semaine_label }}
                                </option>
                            @empty
                                <option disabled>Aucune semaine</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="filter-label">
                        <svg width="13" height="13" style="vertical-align:-2px;opacity:.7;"><use href="#ico-activity"/></svg>
                        Période
                    </div>
                    <div class="premium-select-wrap">
                        <svg width="14" height="14" class="pselect-icon"><use href="#ico-activity"/></svg>
                        <select name="periode" class="premium-select" onchange="this.form.submit()">
                            <option value="semaine" {{ $periode == 'semaine' ? 'selected' : '' }}>Semaine</option>
                            <option value="mois"    {{ $periode == 'mois'    ? 'selected' : '' }}>Mois actuel</option>
                            <option value="annee"   {{ $periode == 'annee'   ? 'selected' : '' }}>Année</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-apply">
                            <svg width="13" height="13" style="vertical-align:-2px;"><use href="#ico-filter"/></svg>
                            Filtrer
                        </button>
                        @if(request()->anyFilled(['point_id', 'month', 'week']) || request('periode', 'mois') != 'mois')
                            <a href="{{ route('dashboard') }}" class="btn-reset" title="Réinitialiser">
                                <svg width="12" height="12"><use href="#ico-reset"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tags filtres actifs --}}
            @php
                $hasActiveFilter = $pointId || $selectedWeek || (is_int($selectedMonth) && $selectedMonth > 0);
            @endphp
            @if($hasActiveFilter)
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @if($pointId)
                        @php $nomF = $allPoints->firstWhere('id', $pointId)?->nom_machine ?? ''; @endphp
                        <span class="filter-context-badge">
                            <svg width="11" height="11" style="vertical-align:-1px;"><use href="#ico-drop"/></svg>
                            {{ $nomF }}
                        </span>
                    @endif
                    @if(is_int($selectedMonth) && $selectedMonth > 0)
                        @php $mNom = $availableMonths[$selectedMonth] ?? ''; @endphp
                        <span class="filter-context-badge">
                            <svg width="11" height="11" style="vertical-align:-1px;"><use href="#ico-cal-month"/></svg>
                            {{ $mNom }} {{ $selectedYear }}
                        </span>
                    @endif
                    @if($selectedWeek)
                        @php $swLabel = $availableWeeks->firstWhere('week_number', $selectedWeek)?->semaine_label ?? 'Sem ' . $selectedWeek; @endphp
                        <span class="filter-context-badge">
                            <svg width="11" height="11" style="vertical-align:-1px;"><use href="#ico-cal-week"/></svg>
                            {{ $swLabel }}
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- ============================================================
         4 KPI — même ligne, couleurs inchangées
         ============================================================ --}}
    <div class="section-label"><i class="bi bi-grid-3x3-gap"></i> Indicateurs clés de performance</div>
    <div class="kpi-grid">

        <div class="kpi-card card-collectes">
            <div class="kpi-card-bg"><i class="bi bi-cash-stack"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">
                    <span class="kpi-icon"><i class="bi bi-wallet2"></i></span>
                    <span class="kpi-title">Total Collectes</span>
                </div>
                <div class="kpi-value">{{ \App\Helpers\FinanceHelper::formatMoney($profitability['collectes'] ?? 0) }}</div>
                <div class="kpi-trend {{ $tendance < 0 ? 'negative' : 'positive' }}">
                    <i class="bi bi-{{ $tendance >= 0 ? 'arrow-up' : 'arrow-down' }}-short"></i>
                    {{ abs($tendance) }}% vs préc.
                </div>
            </div>
            <div class="kpi-glow"></div>
        </div>

        <div class="kpi-card card-depenses-points">
            <div class="kpi-card-bg"><i class="bi bi-geo-alt"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">
                    <span class="kpi-icon"><i class="bi bi-geo-alt"></i></span>
                    <span class="kpi-title">Dépenses Points</span>
                </div>
                <div class="kpi-value">{{ \App\Helpers\FinanceHelper::formatMoney($profitability['depenses_directes'] ?? 0) }}</div>
                <div class="kpi-footer">
                    <i class="bi bi-pie-chart-fill"></i> {{ $depRatio ?? 0 }}% des dépenses
                </div>
            </div>
            <div class="kpi-glow"></div>
        </div>

        <div class="kpi-card card-charges">
            <div class="kpi-card-bg"><i class="bi bi-globe"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">
                    <span class="kpi-icon"><i class="bi bi-globe"></i></span>
                    <span class="kpi-title">Charges Globales</span>
                </div>
                <div class="kpi-value">{{ \App\Helpers\FinanceHelper::formatMoney($profitability['charge_globale'] ?? 0) }}</div>
                <div class="kpi-footer">
                    <i class="bi bi-diagram-3-fill"></i> {{ $globalData['points_actifs'] ?? 0 }} pts actifs
                </div>
            </div>
            <div class="kpi-glow"></div>
        </div>

        @php
            $benefNet = $profitability['benefice'] ?? 0;
            $margeKpi = ($profitability['collectes'] ?? 0) > 0
                ? round($benefNet / ($profitability['collectes'] ?? 1) * 100, 1) : 0;
        @endphp
        <div class="kpi-card {{ $benefNet >= 0 ? 'card-benefice' : 'card-benefice negative' }}">
            <div class="kpi-card-bg"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="kpi-content">
                <div class="kpi-label">
                    <span class="kpi-icon">
                        <i class="bi bi-{{ $benefNet >= 0 ? 'check-circle' : 'exclamation-circle' }}"></i>
                    </span>
                    <span class="kpi-title">Bénéfice Net</span>
                </div>
                <div class="kpi-value">{{ $benefNet >= 0 ? '+' : '' }}{{ \App\Helpers\FinanceHelper::formatMoney($benefNet) }}</div>
                <div class="kpi-footer"><i class="bi bi-percent"></i> Marge {{ $margeKpi }}%</div>
            </div>
            <div class="kpi-glow"></div>
        </div>
    </div>

    {{-- ============================================================
         ZONE PRINCIPALE — 2 colonnes
         ============================================================ --}}
    <div class="main-grid">

        <div class="charts-left">

            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h6 style="margin:0 0 3px;">
                            <i class="bi bi-graph-up"></i>
                            Recettes semaine par semaine
                            @if($modePointUnique ?? false)
                                @php $pNom = $allPoints->firstWhere('id', $pointId)?->nom_machine ?? ''; @endphp
                                <small style="font-size:9.5px;font-weight:400;color:#94a3b8;">— {{ $pNom }}</small>
                            @endif
                        </h6>
                        <div style="font-size:11px;color:#94a3b8;">
                            Chaque point = une semaine — plus c'est haut, plus on a rentré d'argent
                        </div>
                    </div>
                    @php $tg = $tendanceGraphique ?? 0; @endphp
                    <span class="chart-badge {{ $tg < 0 ? 'negative' : ($tg == 0 ? 'neutral' : '') }}" style="flex-shrink:0;">
                        <i class="bi bi-arrow-{{ $tg >= 0 ? 'up' : 'down' }}-short"></i>
                        {{ $tg > 0 ? '+' : '' }}{{ $tg }}% vs sem. préc.
                    </span>
                </div>
                <div class="chart-legend">
                    <div class="chart-legend-item">
                        <div class="chart-legend-dot" style="background:#378ADD; border-radius:50%;"></div>
                        Recettes par semaine
                    </div>
                </div>
                <div class="chart-container-g1">
                    @if(count($chartData['labels'] ?? []) > 0)
                        <canvas id="chartWeekly"></canvas>
                    @else
                        <div class="chart-empty">
                            <i class="bi bi-graph-up"></i>
                            Aucune collecte enregistrée pour cette période
                        </div>
                    @endif
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h6 style="margin:0 0 3px;">
                            <i class="bi bi-calendar-month"></i>
                            Rentabilité mois par mois — {{ $selectedYear }}
                            @if($modePointUnique ?? false)
                                @php $pNom2 = $allPoints->firstWhere('id', $pointId)?->nom_machine ?? ''; @endphp
                                <small style="font-size:9.5px;font-weight:400;color:#94a3b8;">— {{ $pNom2 }}</small>
                            @endif
                        </h6>
                        <div style="font-size:11px;color:#94a3b8;">
                            Point vert = mois rentable — point rouge = mois déficitaire
                        </div>
                    </div>
                </div>
                <div class="chart-legend">
                    <div class="chart-legend-item"><div class="chart-legend-dot" style="background:#378ADD;"></div>Recettes</div>
                    <div class="chart-legend-item"><div class="chart-legend-dot" style="background:#E24B4A;"></div>Dépenses machines</div>
                    <div class="chart-legend-item"><div class="chart-legend-dot" style="background:#BA7517;"></div>Charges fixes</div>
                    <div class="chart-legend-item"><div class="chart-legend-dot" style="background:#639922; border-radius:50%;"></div>Bénéfice net</div>
                </div>
                <div class="chart-container-g2">
                    @if(count($monthlyData['labels'] ?? []) > 0)
                        <canvas id="chartMensuel"></canvas>
                    @else
                        <div class="chart-empty">
                            <i class="bi bi-calendar-month"></i>
                            Aucune donnée mensuelle disponible
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <div class="right-col">

            <div class="top5-card">
                <div class="top5-header">
                    <h6><i class="bi bi-award-fill"></i> Top 5 Rentables</h6>
                    <span class="top5-count">{{ $top5Points->count() }} / {{ $allPoints->count() }} pts</span>
                </div>

                @if($top5Points->count() > 0)
                    @php
                        $maxB = max($top5Points->max('benefice'), 1);
                        $rkCls = ['gold', 'silver', 'bronze'];
                    @endphp
                    <div class="top5-list">
                        @foreach($top5Points as $idx => $item)
                            <div class="top5-row">
                                <div class="rank-badge {{ $rkCls[$idx] ?? '' }}">#{{ $idx + 1 }}</div>
                                <div class="top5-name">{{ $item->nom_machine }}</div>
                                <div class="top5-bar-wrap">
                                    <div class="top5-bar">
                                        <div class="top5-bar-fill"
                                            style="width:{{ max(0, min(100, ($item->benefice / $maxB) * 100)) }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="top5-amount">{{ \App\Helpers\FinanceHelper::formatMoney($item->benefice ?? 0) }}</div>
                                <span class="ratio-pill {{ ($item->ratio ?? 0) >= 0 ? 'ratio-ok' : 'ratio-bad' }}">
                                    {{ $item->ratio }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="chart-empty" style="height:70px;">
                        <i class="bi bi-inbox"></i> Aucune donnée
                    </div>
                @endif
            </div>

            <div class="alerts-card">
                <div class="alerts-head">
                    <i class="bi bi-bell-fill" style="color:#ef4444;"></i>
                    Alertes opérationnelles
                    @php $totalA = $pointsEnPanne->count() + $pointsFaibles->count(); @endphp
                    @if($totalA > 0)
                        <span class="badge-count">{{ $totalA }}</span>
                    @endif
                </div>

                @if($pointsEnPanne->count() > 0)
                    <div class="alert-section-lbl">🔧 Maintenance</div>
                    @foreach($pointsEnPanne as $p)
                        <div class="alert-row warn">
                            <div class="alert-dot dot-warn"></div>
                            <div class="alert-name">{{ $p->nom_machine }}</div>
                            <span class="alert-meta">Inactif</span>
                        </div>
                    @endforeach
                @endif

                @if($pointsFaibles->count() > 0)
                    <div class="alert-section-lbl">
                        📉 Sous le seuil <span class="badge-seuil">{{ $seuilDynamique }}%</span>
                    </div>
                    @foreach($pointsFaibles as $p)
                        <div class="alert-row danger">
                            <div class="alert-dot dot-danger"></div>
                            <div class="alert-name">{{ $p->nom_machine }}</div>
                            <span class="alert-meta">{{ $p->ratio }}%</span>
                        </div>
                    @endforeach
                @endif

                @if($pointsEnPanne->count() === 0 && $pointsFaibles->count() === 0)
                    <div class="alert-row ok">
                        <div class="alert-dot dot-ok"></div>
                        <div class="alert-name">Tout est nominal</div>
                        <span class="alert-meta">✓</span>
                    </div>
                @endif
            </div>

        </div>

    </div>

    </div>

    {{-- ============================================================
         CSS ADDITIONNEL — uniquement pour le wrapper d'icône dans select
         ============================================================ --}}
    <style>
        .premium-select-wrap {
            position: relative;
        }

        .premium-select-wrap .pselect-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 1;
            color: #94a3b8;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Décaler le texte du select pour laisser la place à l'icône */
        .premium-select-wrap .premium-select {
            padding-left: 30px;
        }

        /* Icône dans le label de filtre */
        .filter-label svg use {
            stroke: currentColor;
        }

        /* Icône dans les tags de filtres actifs */
        .filter-context-badge svg {
            stroke: #3b82f6;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Icônes dans btn-apply et btn-reset */
        .btn-apply svg,
        .btn-reset svg {
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
    </style>

@endsection

@section('scripts')
    <script>
        window.chartData = {
            labels:    {!! json_encode($chartData['labels'] ?? []) !!},
            collectes: {!! json_encode($chartData['collectes'] ?? []) !!},
            depenses:  {!! json_encode($chartData['depenses'] ?? []) !!},
            benefices: {!! json_encode($chartData['benefices'] ?? []) !!}
        };

        window.monthlyData = {
            labels:           {!! json_encode($monthlyData['labels'] ?? []) !!},
            collectes:        {!! json_encode($monthlyData['collectes'] ?? []) !!},
            depensesPoints:   {!! json_encode($monthlyData['depensesPoints'] ?? []) !!},
            depensesGlobales: {!! json_encode($monthlyData['depensesGlobales'] ?? []) !!},
            benefices:        {!! json_encode($monthlyData['benefices'] ?? []) !!}
        };

        window.modePointUnique = false;
        window.chartTendance   = { labels: [], collectes: [], depenses: [], benefices: [] };
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection