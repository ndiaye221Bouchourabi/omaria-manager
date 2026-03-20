<?php

namespace App\Http\Controllers;

use App\Services\FinanceService;
use App\Models\Point;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index(Request $request)
    {
        // ================================
        // 1. FILTRES
        // ================================
        $pointId       = $request->input('point_id');
        $selectedYear  = (int) $request->input('year', date('Y'));
        $selectedMonth = $request->input('month') ? (int) $request->input('month') : null;
        $selectedWeek  = $request->input('week')  ? (int) $request->input('week')  : null;
        $periode       = $request->input('periode', 'mois');

        // ================================
        // 2. SEMAINES DISPONIBLES
        // ================================
        $availableWeeks = $this->financeService->getAvailableWeeks(
            $selectedYear, $selectedMonth, $pointId
        );

        // ================================
        // 3. GESTION DES DATES
        // ================================
        $date = Carbon::create($selectedYear, 1, 1);

        if ($periode === 'annee') {
            $dateDebut     = $date->copy()->startOfYear();
            $dateFin       = $date->copy()->endOfYear();
            $dateDebutPrec = $date->copy()->subYear()->startOfYear();
            $dateFinPrec   = $date->copy()->subYear()->endOfYear();
        } elseif ($periode === 'semaine') {
            $dateDebut     = Carbon::now()->startOfWeek();
            $dateFin       = Carbon::now()->endOfWeek();
            $dateDebutPrec = Carbon::now()->subWeek()->startOfWeek();
            $dateFinPrec   = Carbon::now()->subWeek()->endOfWeek();
        } else {
            $month         = $selectedMonth ?? date('n');
            $dateDebut     = Carbon::create($selectedYear, $month, 1)->startOfMonth();
            $dateFin       = Carbon::create($selectedYear, $month, 1)->endOfMonth();
            $dateDebutPrec = Carbon::create($selectedYear, $month, 1)->subMonth()->startOfMonth();
            $dateFinPrec   = Carbon::create($selectedYear, $month, 1)->subMonth()->endOfMonth();
        }

        if ($selectedWeek) {
            $dateDebut     = Carbon::now()->setISODate($selectedYear, $selectedWeek)->startOfWeek();
            $dateFin       = Carbon::now()->setISODate($selectedYear, $selectedWeek)->endOfWeek();
            $dateDebutPrec = $dateDebut->copy()->subWeek()->startOfWeek();
            $dateFinPrec   = $dateDebut->copy()->subWeek()->endOfWeek();
        }

        // ================================
        // 4. DONNÉES GLOBALES
        // ================================
        $globalData     = $this->financeService->calculateGlobalChargePerPoint($dateDebut, $dateFin, $pointId);
        $globalDataPrec = $this->financeService->calculateGlobalChargePerPoint($dateDebutPrec, $dateFinPrec, $pointId);

        // ================================
        // 5. KPI
        // ================================
        $totalCollectes        = $globalData['total_collectes'];
        $totalDepensesDirectes = $globalData['total_depenses_directes'];
        $totalChargesGlobales  = $globalData['total_global_depenses'];
        $chargeGlobale         = $pointId ? $globalData['par_point'] : $totalChargesGlobales;
        $benefice              = $totalCollectes - ($totalDepensesDirectes + $chargeGlobale);
        $totalDepenses         = $totalDepensesDirectes + $chargeGlobale;

        $totalPrecedent = $globalDataPrec['total_collectes'];
        $tendance = 0;
        if ($totalPrecedent > 0) {
            $tendance = (($totalCollectes - $totalPrecedent) / $totalPrecedent) * 100;
        } elseif ($totalCollectes > 0) {
            $tendance = 100;
        }

        // ================================
        // 6. RENTABILITÉ PAR POINT
        // ================================
        $points         = Point::where('status', 'Actif')->get();
        $chargeParPoint = $globalData['par_point'];

        $rentabilitePoints = $points->map(function ($point) use ($dateDebut, $dateFin, $chargeParPoint) {
            $data = $this->financeService->calculatePointProfitability($point, $dateDebut, $dateFin, $chargeParPoint);
            return (object) [
                'id'                => $point->id,
                'nom_machine'       => $point->nom_machine,
                'collectes'         => $data['collectes'],
                'depenses_directes' => $data['depenses_directes'],
                'charge_globale'    => $data['charge_globale'],
                'benefice'          => $data['benefice'],
                'ratio'             => $data['ratio'],
            ];
        });

        // ================================
        // 7. TOP 5 + ALERTES
        // ================================
        $top5Points     = $rentabilitePoints->sortByDesc('benefice')->take(5)->values();
        $seuilDynamique = $this->financeService->getDynamicThreshold($rentabilitePoints);
        $pointsFaibles  = $rentabilitePoints->filter(fn($p) => $p->ratio < $seuilDynamique)->values();
        $pointsEnPanne  = Point::where('status', 'Inactif')->get();

        // ================================
        // 8. GRAPHIQUE G1 — Collectes par semaine
        // Connecté à tous les filtres
        // ================================
        $chartData = $this->financeService->getWeeklyChartData(
            $pointId, $selectedYear, $selectedMonth, $selectedWeek
        );

        // Tendance G1 (dernière vs avant-dernière semaine)
        $tendanceGraphique = 0;
        $collectesSemaines = $chartData['collectes'];
        if (count($collectesSemaines) >= 2) {
            $derniere      = end($collectesSemaines);
            $avantDerniere = $collectesSemaines[count($collectesSemaines) - 2];
            if ($avantDerniere > 0) {
                $tendanceGraphique = round((($derniere - $avantDerniere) / $avantDerniere) * 100, 1);
            } elseif ($derniere > 0) {
                $tendanceGraphique = 100;
            }
        }

        // ================================
        // 9. GRAPHIQUE G2 — Rentabilité par mois
        // ✅ NOUVEAU : données mensuelles agrégées
        // Connecté aux filtres : année + point_id
        // (le filtre mois ne s'applique pas ici car on VEUT voir tous les mois)
        // ================================
        $monthlyData = $this->financeService->getMonthlyChartData(
            $selectedYear,
            $pointId   // si un point est filtré → sa quote-part des charges globales
        );

        // ================================
        // 10. LISTES FILTRES
        // ================================
        $availableYears  = $this->financeService->getAvailableYears();
        $availableMonths = $this->financeService->getAvailableMonths($selectedYear);

        // ================================
        // 11. KPI SUPPLÉMENTAIRES
        // ================================
        $marge    = $totalCollectes > 0 ? round($benefice / $totalCollectes * 100, 1) : 0;
        $depRatio = ($totalDepensesDirectes + $totalChargesGlobales) > 0
            ? round($totalDepensesDirectes / ($totalDepensesDirectes + $totalChargesGlobales) * 100, 1) : 0;

        $revenuMoyenParSemaine = $this->financeService->getAverageRevenuePerWeek($dateDebut, $dateFin, $pointId);
        $tauxCouverture        = $this->financeService->getCoverageRate($totalCollectes, $totalDepenses);
        $meilleurPoint         = $this->financeService->getBestPoint($dateDebut, $dateFin);
        $saisonnalite          = $this->financeService->getSeasonalityIndicators($dateDebut, $dateFin, $pointId);

        // ================================
        // 12. RETURN VIEW
        // ================================
        return view('dashboard', [
            'allPoints'          => Point::all(),
            'profitability'      => [
                'collectes'         => $totalCollectes,
                'depenses_directes' => $totalDepensesDirectes,
                'charge_globale'    => $chargeGlobale,
                'benefice'          => $benefice,
            ],
            'globalData'         => $globalData,
            'rentabilitePoints'  => $rentabilitePoints,
            'top5Points'         => $top5Points,
            'pointsFaibles'      => $pointsFaibles,
            'pointsEnPanne'      => $pointsEnPanne,
            'seuilDynamique'     => $seuilDynamique,

            // G1 : semaines
            'chartData'          => $chartData,
            'tendanceGraphique'  => $tendanceGraphique,

            // ✅ G2 : mois (nouveau)
            'monthlyData'        => $monthlyData,

            'selectedYear'       => $selectedYear,
            'selectedMonth'      => $selectedMonth,
            'selectedWeek'       => $selectedWeek,
            'pointId'            => $pointId,
            'periode'            => $periode,

            'availableYears'     => $availableYears,
            'availableMonths'    => $availableMonths,
            'availableWeeks'     => $availableWeeks,

            'monthIcons'         => [
                1=>'❄️',2=>'🌨️',3=>'🌱',4=>'🌸',
                5=>'☀️',6=>'🏖️',7=>'☀️',8=>'🔥',
                9=>'🍂',10=>'🍁',11=>'☁️',12=>'🎄',
            ],

            'tendance'              => round($tendance, 1),
            'marge'                 => $marge,
            'depRatio'              => $depRatio,
            'revenuMoyenParSemaine' => $revenuMoyenParSemaine,
            'tauxCouverture'        => $tauxCouverture,
            'meilleurPoint'         => $meilleurPoint,
            'saisonnalite'          => $saisonnalite,
        ]);
    }
}
