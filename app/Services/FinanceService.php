<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceService
{
    /**
     * CALCUL GLOBAL
     */
    public function calculateGlobalChargePerPoint($dateDebut, $dateFin, $pointId = null)
    {
        $collectesQuery = DB::table('collectes')
            ->whereBetween('date_collecte', [$dateDebut, $dateFin]);
        if ($pointId)
            $collectesQuery->where('point_id', $pointId);
        $totalCollectes = $collectesQuery->sum('montant');

        $depensesDirectesQuery = DB::table('depenses')
            ->where('portee', 'point')
            ->whereBetween('date_depense', [$dateDebut, $dateFin]);
        if ($pointId)
            $depensesDirectesQuery->where('point_id', $pointId);
        $totalDepensesDirectes = $depensesDirectesQuery->sum('montant');

        $depensesGlobales = DB::table('depenses')
            ->where('portee', 'globale')
            ->whereBetween('date_depense', [$dateDebut, $dateFin])
            ->sum('montant');

        $pointsActifs = DB::table('points')->where('status', 'Actif')->count();
        $chargeParPoint = $pointsActifs > 0 ? $depensesGlobales / $pointsActifs : 0;

        return [
            'total_collectes' => $totalCollectes,
            'total_depenses_directes' => $totalDepensesDirectes,
            'total_global_depenses' => $depensesGlobales,
            'points_actifs' => $pointsActifs,
            'par_point' => $chargeParPoint,
        ];
    }

    /**
     * RENTABILITÉ PAR POINT
     */
    public function calculatePointProfitability($point, $dateDebut, $dateFin, $chargeParPoint)
    {
        $collectes = DB::table('collectes')
            ->where('point_id', $point->id)
            ->whereBetween('date_collecte', [$dateDebut, $dateFin])
            ->sum('montant');

        $depensesDirectes = DB::table('depenses')
            ->where('point_id', $point->id)
            ->where('portee', 'point')
            ->whereBetween('date_depense', [$dateDebut, $dateFin])
            ->sum('montant');

        $chargeGlobale = $chargeParPoint;
        $benefice = $collectes - ($depensesDirectes + $chargeGlobale);
        $ratio = $collectes > 0 ? ($benefice / $collectes) * 100 : 0;

        return [
            'collectes' => $collectes,
            'depenses_directes' => $depensesDirectes,
            'charge_globale' => $chargeGlobale,
            'benefice' => $benefice,
            'ratio' => round($ratio, 2),
        ];
    }

    /**
     * GRAPHIQUE HEBDOMADAIRE
     *
     * ✅ CORRECTIF PRINCIPAL :
     * Si aucune collecte trouvée pour la période filtrée (mois/semaine),
     * on fait un fallback sur TOUTES les semaines disponibles de l'année.
     * Ainsi le graphique ne reste jamais vide tant qu'il y a des données en base.
     */
    public function getWeeklyChartData($pointId = null, $year = null, $month = null, $selectedSemaine = null)
    {
        $labels = $collectes = $depenses = $benefices = [];

        // 1. Récupérer les semaines selon les filtres demandés
        $semainesReelles = $this->getAvailableWeeks($year, $month, $pointId);

        // ✅ Fallback : si le mois filtré ne contient aucune semaine,
        //    on prend toutes les semaines de l'année (ou toutes en base)
        if ($semainesReelles->isEmpty()) {
            $semainesReelles = $this->getAvailableWeeks($year, null, $pointId);
        }
        // Fallback ultime : toutes les semaines sans filtre d'année
        if ($semainesReelles->isEmpty()) {
            $semainesReelles = $this->getAvailableWeeks(null, null, $pointId);
        }

        // 2. Filtre semaine précise si sélectionnée
        if ($selectedSemaine !== null) {
            $semainesReelles = $semainesReelles
                ->filter(fn($s) => $s->week_number == $selectedSemaine)
                ->values();
        }

        // 3. Construire les données pour chaque semaine
        foreach ($semainesReelles as $sem) {
            $start = Carbon::now()
                ->setISODate($sem->year_number, $sem->week_number)
                ->startOfWeek();
            $end = $start->copy()->endOfWeek();

            // ✅ Collectes groupées par le champ `semaine` enregistré
            $collecteQuery = DB::table('collectes')
                ->where('semaine', $sem->semaine_label);
            if ($pointId)
                $collecteQuery->where('point_id', $pointId);
            $totalCollecte = $collecteQuery->sum('montant');

            // Dépenses sur la plage de dates de la semaine
            $depenseQuery = DB::table('depenses')
                ->whereBetween('date_depense', [$start, $end]);
            if ($pointId)
                $depenseQuery->where('point_id', $pointId);
            $totalDepense = $depenseQuery->sum('montant');

            $benefice = $totalCollecte - $totalDepense;

            $labels[] = $sem->semaine_label;
            $collectes[] = $totalCollecte;
            $depenses[] = $totalDepense;
            $benefices[] = $benefice;
        }

        return [
            'labels' => $labels,
            'collectes' => $collectes,
            'depenses' => $depenses,
            'benefices' => $benefices,
            'current' => !empty($collectes) ? end($collectes) : 0,
            'previous' => count($collectes) >= 2 ? $collectes[count($collectes) - 2] : 0,
        ];
    }

    /**
     * SEMAINES DISPONIBLES
     * Depuis le champ `semaine` de collectes UNION date_depense
     */
    public function getAvailableWeeks($year = null, $month = null, $pointId = null)
    {
        $collectesQuery = DB::table('collectes')
            ->selectRaw('semaine AS semaine_label, YEAR(date_collecte) AS year_number, WEEK(date_collecte,1) AS week_number')
            ->distinct();
        if ($year)
            $collectesQuery->whereYear('date_collecte', $year);
        if ($month)
            $collectesQuery->whereMonth('date_collecte', $month);
        if ($pointId)
            $collectesQuery->where('point_id', $pointId);

        $depensesQuery = DB::table('depenses')
            ->selectRaw("CONCAT('Sem ',WEEK(date_depense,1),'-',YEAR(date_depense)) AS semaine_label, YEAR(date_depense) AS year_number, WEEK(date_depense,1) AS week_number")
            ->distinct();
        if ($year)
            $depensesQuery->whereYear('date_depense', $year);
        if ($month)
            $depensesQuery->whereMonth('date_depense', $month);
        if ($pointId)
            $depensesQuery->where('point_id', $pointId)->where('portee', 'point');

        $toutes = $collectesQuery->get();
        $depSemaines = $depensesQuery->get();

        $merged = $toutes->keyBy(fn($s) => $s->year_number . '-' . $s->week_number);
        foreach ($depSemaines as $dep) {
            $key = $dep->year_number . '-' . $dep->week_number;
            if (!$merged->has($key))
                $merged->put($key, $dep);
        }

        return $merged
            ->sortBy([['year_number', 'asc'], ['week_number', 'asc']])
            ->values();
    }

    /**
     * REVENU MOYEN PAR SEMAINE
     */
    public function getAverageRevenuePerWeek($dateDebut, $dateFin, $pointId = null)
    {
        $query = DB::table('collectes')->whereBetween('date_collecte', [$dateDebut, $dateFin]);
        if ($pointId)
            $query->where('point_id', $pointId);
        $total = (clone $query)->sum('montant');
        $nbSemaines = (clone $query)->distinct()->count('semaine');
        return $nbSemaines > 0 ? round($total / $nbSemaines, 3) : 0;
    }

    /**
     * COLLECTES GROUPÉES PAR SEMAINE
     */
    public function getCollectesBySemaine($dateDebut, $dateFin, $pointId = null)
    {
        $query = DB::table('collectes')
            ->whereBetween('date_collecte', [$dateDebut, $dateFin])
            ->selectRaw('semaine, SUM(montant) as total')
            ->groupBy('semaine', 'date_collecte')
            ->orderBy('date_collecte');
        if ($pointId)
            $query->where('point_id', $pointId);
        return $query->get();
    }

    /**
     * DONNÉES GRAPHIQUE MENSUEL — G2
     *
     * Retourne par mois (connecté aux filtres année + point_id) :
     * - collectes totales du mois
     * - dépenses directes par point du mois
     * - charges globales du mois
     * - bénéfice net = collectes - dépenses points - charges globales / nb_points
     *
     * @param int      $year    Année sélectionnée
     * @param int|null $pointId Point filtré (null = tous)
     * @return array
     */
    public function getMonthlyChartData(int $year, $pointId = null): array
    {
        $labels = [];
        $collectes = [];
        $depensesPoints = [];
        $depensesGlobales = [];
        $benefices = [];

        $moisLabels = [
            1 => 'Jan',
            2 => 'Fév',
            3 => 'Mar',
            4 => 'Avr',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juil',
            8 => 'Août',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Déc',
        ];

        /* Quels mois ont des données pour cette année ? */
        $moisAvecDonnees = DB::table('collectes')
            ->selectRaw('MONTH(date_collecte) as mois')
            ->whereYear('date_collecte', $year)
            ->when($pointId, fn($q) => $q->where('point_id', $pointId))
            ->distinct()
            ->orderBy('mois')
            ->pluck('mois');

        /* Si aucun mois de collecte, on regarde les dépenses */
        if ($moisAvecDonnees->isEmpty()) {
            $moisAvecDonnees = DB::table('depenses')
                ->selectRaw('MONTH(date_depense) as mois')
                ->whereYear('date_depense', $year)
                ->distinct()
                ->orderBy('mois')
                ->pluck('mois');
        }

        if ($moisAvecDonnees->isEmpty()) {
            return compact('labels', 'collectes', 'depensesPoints', 'depensesGlobales', 'benefices');
        }

        $nbPointsActifs = DB::table('points')->where('status', 'Actif')->count() ?: 1;

        foreach ($moisAvecDonnees as $mois) {
            /* Collectes du mois */
            $qC = DB::table('collectes')
                ->whereYear('date_collecte', $year)
                ->whereMonth('date_collecte', $mois);
            if ($pointId)
                $qC->where('point_id', $pointId);
            $totalC = (float) $qC->sum('montant');

            /* Dépenses par point du mois */
            $qDP = DB::table('depenses')
                ->where('portee', 'point')
                ->whereYear('date_depense', $year)
                ->whereMonth('date_depense', $mois);
            if ($pointId)
                $qDP->where('point_id', $pointId);
            $totalDP = (float) $qDP->sum('montant');

            /* Charges globales du mois (répartie si point filtré) */
            $qDG = DB::table('depenses')
                ->where('portee', 'globale')
                ->whereYear('date_depense', $year)
                ->whereMonth('date_depense', $mois);
            $totalDG = (float) $qDG->sum('montant');

            /* Si un point est filtré, on lui attribue sa quote-part */
            $chargeGlobale = $pointId
                ? round($totalDG / $nbPointsActifs, 3)
                : $totalDG;

            $benefice = round($totalC - $totalDP - $chargeGlobale, 3);

            $labels[] = $moisLabels[$mois] . ' ' . $year;
            $collectes[] = round($totalC, 3);
            $depensesPoints[] = round($totalDP, 3);
            $depensesGlobales[] = round($chargeGlobale, 3);
            $benefices[] = $benefice;
        }

        return compact('labels', 'collectes', 'depensesPoints', 'depensesGlobales', 'benefices');
    }

    /**
     * TAUX DE COUVERTURE
     */
    public function getCoverageRate($totalCollectes, $totalDepenses)
    {
        if ($totalDepenses <= 0)
            return $totalCollectes > 0 ? 999 : 0;
        return round(($totalCollectes / $totalDepenses) * 100, 1);
    }

    /**
     * MEILLEUR POINT
     */
    public function getBestPoint($dateDebut, $dateFin)
    {
        return DB::table('collectes')
            ->join('points', 'collectes.point_id', '=', 'points.id')
            ->whereBetween('date_collecte', [$dateDebut, $dateFin])
            ->selectRaw('points.nom_machine, SUM(collectes.montant) as total')
            ->groupBy('points.id', 'points.nom_machine')
            ->orderByDesc('total')
            ->first();
    }

    /**
     * SEUIL DYNAMIQUE
     */
    public function getDynamicThreshold($rentabilitePoints)
    {
        $ratios = $rentabilitePoints->pluck('ratio');
        if ($ratios->isEmpty())
            return 10;
        $moyenne = $ratios->avg();
        $variance = $ratios->map(fn($r) => pow($r - $moyenne, 2))->avg();
        $ecartType = sqrt($variance);
        return max(0, round($moyenne - $ecartType, 2));
    }

    /**
     * SAISONNALITÉ
     */
    public function getSeasonalityIndicators($dateDebut, $dateFin, $pointId = null)
    {
        $semaines = $this->getCollectesBySemaine($dateDebut, $dateFin, $pointId);
        if ($semaines->isEmpty()) {
            return ['semaine_forte' => null, 'semaine_faible' => null, 'max_montant' => 0, 'min_montant' => 0];
        }
        $max = $semaines->sortByDesc('total')->first();
        $min = $semaines->sortBy('total')->first();
        return [
            'semaine_forte' => $max->semaine,
            'semaine_faible' => $min->semaine,
            'max_montant' => $max->total,
            'min_montant' => $min->total,
        ];
    }

    /**
     * CALCULATE TREND
     */
    public function calculateTrend($current, $previous)
    {
        if ($previous <= 0)
            return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * ANNÉES DISPONIBLES
     */
    public function getAvailableYears()
    {
        return DB::table('collectes')
            ->selectRaw('YEAR(date_collecte) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }

    /**
     * MOIS DISPONIBLES
     */
    public function getAvailableMonths($year)
    {
        $months = DB::table('collectes')
            ->selectRaw('MONTH(date_collecte) as month')
            ->whereYear('date_collecte', $year)
            ->distinct()
            ->pluck('month');

        $labels = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];

        $result = [];
        foreach ($months as $m)
            $result[$m] = $labels[$m];
        return $result;
    }
}
