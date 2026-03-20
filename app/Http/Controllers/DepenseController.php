<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Point;
use App\Helpers\FinanceHelper;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::with('point');
        $isFiltered = false;

        if ($request->filled('point_id')) {
            $query->where('point_id', $request->point_id);
            $isFiltered = true;
        }
        if ($request->filled('type')) {
            $query->where('type_depense', $request->type);
            $isFiltered = true;
        }
        if ($request->filled('portee') && $request->portee !== 'toutes') {
            $query->where('portee', $request->portee);
            $isFiltered = true;
        }
        if ($request->filled('mois')) {
            $query->whereMonth('date_depense', $request->mois);
            $isFiltered = true;
        }
        if ($request->filled('annee')) {
            $query->whereYear('date_depense', $request->annee);
            $isFiltered = true;
        }

        $depenses = $query->orderBy('date_depense', 'desc')->get();
        $totalFiltré = FinanceHelper::roundMoney($depenses->sum('montant'));
        $totalSpecifique = FinanceHelper::roundMoney(Depense::where('portee', 'point')->sum('montant'));
        $totalGlobal = FinanceHelper::roundMoney(Depense::where('portee', 'globale')->sum('montant'));

        $stats = [
            'nombre_depenses' => $depenses->count(),
            'moyenne' => $depenses->count() > 0 ? FinanceHelper::roundMoney($depenses->avg('montant')) : 0,
            'max' => FinanceHelper::roundMoney($depenses->max('montant') ?? 0),
            'min' => FinanceHelper::roundMoney($depenses->min('montant') ?? 0),
        ];

        $points = Point::all();
        $types = Depense::distinct()->pluck('type_depense');
        $anneesDisponibles = Depense::selectRaw('YEAR(date_depense) as annee')->distinct()->orderBy('annee', 'desc')->pluck('annee');

        return view('depenses.index', compact(
            'depenses',
            'points',
            'types',
            'totalFiltré',
            'totalSpecifique',
            'totalGlobal',
            'isFiltered',
            'stats',
            'anneesDisponibles'
        ));
    }

    public function store(Request $request)
    {
        $montant = str_replace(',', '.', $request->montant);
        $montant = round(floatval($montant), 3);
        $request->merge(['montant' => $montant]);

        $request->validate([
            'montant' => 'required|numeric|min:0',
            'type_depense' => 'required|string',
            'portee' => 'required|in:point,globale',
            'date_depense' => 'required|date',
            'point_id' => 'required_if:portee,point',
            'description' => 'nullable|string',
        ]);

        Depense::create([
            'point_id' => $request->portee == 'point' ? $request->point_id : null,
            'type_depense' => $request->type_depense,
            'montant' => $montant,
            'portee' => $request->portee,
            'description' => $request->description,
            'date_depense' => $request->date_depense,
        ]);

        return redirect()->back()->with('success', '✅ Dépense enregistrée avec succès');
    }

    public function update(Request $request, $id)
    {
        $depense = Depense::with('point')->findOrFail($id);

        // 📸 Snapshot AVANT modification
        $typeAvant = $depense->type_depense;
        $montantAvant = number_format($depense->montant, 0, ',', ' ');
        $porteeAvant = $depense->portee === 'point'
            ? ($depense->point->nom_machine ?? 'N/A')
            : 'Globale';

        $montant = str_replace(',', '.', $request->montant);
        $montant = round(floatval($montant), 3);
        $request->merge(['montant' => $montant]);

        $request->validate([
            'montant' => 'required|numeric|min:0',
            'type_depense' => 'required|string',
            'date_depense' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $depense->update([
            'montant' => $montant,
            'type_depense' => $request->type_depense,
            'date_depense' => $request->date_depense,
            'description' => $request->description,
        ]);

        // ✅ Log modification dépense
        ActivityLogger::log(
            action: 'Modification dépense',
            module: 'depenses',
            detail: "Dépense #{$depense->id} — {$porteeAvant} | "
            . "Type : {$typeAvant} | "
            . "Montant : {$montantAvant} → "
            . number_format($montant, 0, ',', ' ') . " FCFA"
        );

        return redirect()->back()->with('success', '✅ Dépense mise à jour avec succès');
    }

    public function destroy($id)
    {
        $depense = Depense::with('point')->findOrFail($id);

        // 📸 Snapshot AVANT suppression
        $porteeLabel = $depense->portee === 'point'
            ? ($depense->point->nom_machine ?? 'N/A')
            : 'Globale';
        $montant = number_format($depense->montant, 0, ',', ' ');
        $type = $depense->type_depense;

        $depense->delete();

        // ✅ Log suppression dépense
        ActivityLogger::log(
            action: 'Suppression dépense',
            module: 'depenses',
            detail: "Dépense #{$id} — {$porteeLabel} | Type : {$type} | Montant : {$montant} FCFA"
        );

        return redirect()->back()->with('success', '✅ Dépense supprimée avec succès');
    }
}