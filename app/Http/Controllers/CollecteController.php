<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Point;
use App\Helpers\FinanceHelper;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CollecteController extends Controller
{
    public function index(Request $request)
    {
        $query = Collecte::with('point');

        if ($request->filled('point_id')) {
            $query->where('point_id', $request->point_id);
        }

        if ($request->filled('semaine')) {
            $query->where('semaine', $request->semaine);
        }

        if ($request->filled('mois')) {
            $query->whereMonth('date_collecte', $request->mois);
        }

        if ($request->filled('annee')) {
            $query->whereYear('date_collecte', $request->annee);
        }

        $totalFiltre = $query->sum('montant');
        $moyenneFiltre = $query->avg('montant') ?? 0;
        $nombreCollectes = $query->count();
        $collectes = $query->orderBy('date_collecte', 'desc')->get();
        $points = Point::all();

        $semainesDisponibles = Collecte::selectRaw('semaine, MAX(date_collecte) as derniere_date')
            ->whereNotNull('semaine')
            ->groupBy('semaine')
            ->orderBy('derniere_date', 'desc')
            ->pluck('semaine');

        $anneesDisponibles = Collecte::selectRaw('YEAR(date_collecte) as annee')
            ->distinct()
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        $stats = [
            'total_global' => Collecte::sum('montant'),
            'moyenne_globale' => Collecte::avg('montant') ?? 0,
            'total_points' => $points->count(),
            'points_actifs' => $points->where('status', 'Actif')->count(),
        ];

        return view('collectes.index', compact(
            'collectes',
            'points',
            'semainesDisponibles',
            'anneesDisponibles',
            'totalFiltre',
            'moyenneFiltre',
            'nombreCollectes',
            'stats'
        ));
    }

    public function store(Request $request)
    {
        $montant = str_replace(',', '.', $request->montant);
        $request->merge(['montant' => $montant]);

        $data = $request->validate([
            'point_id' => 'required|exists:points,id',
            'montant' => 'required|numeric|min:0',
            'date_collecte' => 'required|date',
            'semaine' => 'nullable|string|max:50',
        ]);

        if (empty($data['semaine'])) {
            $date = Carbon::parse($data['date_collecte']);
            $data['semaine'] = 'S' . $date->weekOfYear . '-' . $date->year;
        }

        $collecte = Collecte::create($data);

        // ✅ Log — saisie collecte (pas tracé selon nos règles, mais on garde pour cohérence)
        return redirect()->back()->with('success', '✅ Collecte enregistrée avec succès');
    }

    public function update(Request $request, $id)
    {
        $collecte = Collecte::findOrFail($id);

        // 📸 Snapshot AVANT modification pour le log
        $pointAvant = $collecte->point->nom_machine ?? 'N/A';
        $montantAvant = number_format($collecte->montant, 0, ',', ' ');
        $semaineAvant = $collecte->semaine;

        $montant = str_replace(',', '.', $request->montant);
        $request->merge(['montant' => $montant]);

        $data = $request->validate([
            'point_id' => 'required|exists:points,id',
            'montant' => 'required|numeric|min:0',
            'date_collecte' => 'required|date',
            'semaine' => 'required|string|max:50',
        ]);

        $collecte->update($data);

        // ✅ Log modification collecte
        ActivityLogger::log(
            action: 'Modification collecte',
            module: 'collectes',
            detail: "Collecte #{$collecte->id} — {$pointAvant} | "
            . "Semaine : {$semaineAvant} | "
            . "Montant : {$montantAvant} → "
            . number_format($request->montant, 0, ',', ' ') . " FCFA"
        );

        return redirect()->back()->with('success', '✅ Collecte mise à jour avec succès');
    }

    public function destroy($id)
    {
        $collecte = Collecte::with('point')->findOrFail($id);

        // 📸 Snapshot AVANT suppression
        $pointNom = $collecte->point->nom_machine ?? 'N/A';
        $montant = number_format($collecte->montant, 0, ',', ' ');
        $semaine = $collecte->semaine;

        $collecte->delete();

        // ✅ Log suppression collecte
        ActivityLogger::log(
            action: 'Suppression collecte',
            module: 'collectes',
            detail: "Collecte #{$id} — {$pointNom} | Semaine : {$semaine} | Montant : {$montant} FCFA"
        );

        return redirect()->back()->with('success', '✅ Collecte supprimée avec succès');
    }

    public function getStats()
    {
        return response()->json([
            'total_jour' => Collecte::whereDate('date_collecte', today())->sum('montant'),
            'total_semaine' => Collecte::whereBetween('date_collecte', [now()->startOfWeek(), now()->endOfWeek()])->sum('montant'),
            'total_mois' => Collecte::whereMonth('date_collecte', now()->month)->whereYear('date_collecte', now()->year)->sum('montant'),
        ]);
    }
}