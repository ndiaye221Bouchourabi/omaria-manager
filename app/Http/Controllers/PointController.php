<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\Collecte;
use App\Models\Depense;
use Illuminate\Http\Request;

/**
 * Contrôleur de gestion des points de distribution
 * Gère toutes les opérations CRUD pour les points
 * Inclut des statistiques avancées et une API pour les détails
 * 
 * @package App\Http\Controllers
 */
class PointController extends Controller
{
    /**
     * Affiche la liste des points avec statistiques avancées
     * 
     * @return \Illuminate\View\View Vue avec la liste des points et leurs statistiques
     * 
     * Statistiques fournies :
     * - total : Nombre total de points
     * - actifs : Points en service
     * - inactifs : Points en maintenance
     * - taux_activite : Pourcentage de points actifs
     * - total_collectes : Somme de toutes les collectes
     * - total_depenses : Somme de toutes les dépenses
     */
    public function index()
    {
        // ====================================================================
        // 1. RÉCUPÉRATION DES POINTS AVEC COMPTAGES
        // ====================================================================

        /**
         * withCount(['collectes', 'depenses']) ajoute deux colonnes virtuelles :
         * - collectes_count : nombre de collectes liées à ce point
         * - depenses_count : nombre de dépenses liées à ce point
         * 
         * Cela évite des requêtes supplémentaires dans la vue
         */
        $points = Point::withCount(['collectes', 'depenses'])->get();

        // ====================================================================
        // 2. CALCUL DES STATISTIQUES AVANCÉES
        // ====================================================================

        /**
         * Les statistiques sont calculées en mémoire à partir de la collection $points
         * C'est plus performant que de faire des requêtes SQL supplémentaires
         */
        $stats = [
            // Nombre total de points
            'total' => $points->count(),

            // Points avec statut 'Actif'
            'actifs' => $points->where('status', 'Actif')->count(),

            // Points avec statut 'Inactif'
            'inactifs' => $points->where('status', 'Inactif')->count(),

            /**
             * Taux d'activité = (points actifs / total) * 100
             * Protection contre la division par zéro
             * Arrondi à 1 décimale pour l'affichage
             */
            'taux_activite' => $points->count() > 0
                ? round($points->where('status', 'Actif')->count() / $points->count() * 100, 1)
                : 0,

            // Totaux financiers globaux (indépendants des points)
            'total_collectes' => Collecte::sum('montant'),
            'total_depenses' => Depense::sum('montant'),
        ];

        // ====================================================================
        // 3. RETOUR DE LA VUE AVEC LES DONNÉES
        // ====================================================================

        return view('points.index', compact('points', 'stats'));
    }

    /**
     * Enregistre un nouveau point de distribution
     * 
     * @param Request $request Données du formulaire
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste avec message
     * 
     * @throws \Illuminate\Validation\ValidationException En cas de données invalides
     * 
     * Règles de validation :
     * - nom_machine : obligatoire, unique dans la table points
     * - lieu : obligatoire
     * - status : obligatoire, doit être 'Actif' ou 'Inactif'
     */
    public function store(Request $request)
    {
        // ====================================================================
        // 1. VALIDATION DES DONNÉES
        // ====================================================================

        $request->validate([
            // 'unique:points,nom_machine' vérifie que le nom n'existe pas déjà
            'nom_machine' => 'required|unique:points,nom_machine',
            'lieu' => 'required',
            // 'in:Actif,Inactif' limite les valeurs possibles
            'status' => 'required|in:Actif,Inactif'
        ]);

        // ====================================================================
        // 2. CRÉATION DU POINT
        // ====================================================================

        // create() remplit automatiquement les champs fillable du modèle
        Point::create($request->all());

        // ====================================================================
        // 3. REDIRECTION AVEC MESSAGE DE SUCCÈS
        // ====================================================================

        return redirect()->route('points.index')
            ->with('success', '✅ Point de distribution ajouté avec succès');
    }

    /**
     * Met à jour un point existant
     * 
     * @param Request $request Données du formulaire
     * @param int $id Identifiant du point à modifier
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste avec message
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si le point n'existe pas
     * 
     * Particularité : La règle unique ignore l'enregistrement en cours
     * Syntaxe : 'unique:points,nom_machine,' . $id
     */
    public function update(Request $request, $id)
    {
        // ====================================================================
        // 1. RECHERCHE DU POINT
        // ====================================================================

        $point = Point::findOrFail($id);

        // ====================================================================
        // 2. VALIDATION AVEC IGNORATION DE L'ID ACTUEL
        // ====================================================================

        $request->validate([
            /**
             * La règle 'unique:points,nom_machine,' . $id signifie :
             * "Vérifie l'unicité dans la table points, colonne nom_machine,
             * mais ignore l'enregistrement avec l'id $id"
             * 
             * Cela permet de garder le même nom sans erreur
             */
            'nom_machine' => 'required|unique:points,nom_machine,' . $id,
            'lieu' => 'required',
            'status' => 'required|in:Actif,Inactif'
        ]);

        // ====================================================================
        // 3. MISE À JOUR
        // ====================================================================

        $point->update($request->all());

        return redirect()->route('points.index')
            ->with('success', '✅ Point mis à jour avec succès');
    }

    /**
     * Supprime un point
     * 
     * @param int $id Identifiant du point à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection vers la liste avec message
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si le point n'existe pas
     * 
     * Protection : Empêche la suppression si le point a des transactions associées
     * Cela maintient l'intégrité référentielle des données
     */
    public function destroy($id)
    {
        // ====================================================================
        // 1. RECHERCHE DU POINT
        // ====================================================================

        $point = Point::findOrFail($id);

        // ====================================================================
        // 2. VÉRIFICATION DES DÉPENDANCES
        // ====================================================================

        /**
         * Compte les collectes et dépenses liées à ce point
         * Si > 0, le point ne peut pas être supprimé car :
         * - Les données historiques seraient perdues
         * - Les rapports deviendraient incohérents
         */
        if ($point->collectes()->count() > 0 || $point->depenses()->count() > 0) {
            return redirect()->route('points.index')
                ->with('error', '❌ Impossible de supprimer : ce point a des transactions associées');
        }

        // ====================================================================
        // 3. SUPPRESSION (UNIQUEMENT SI SANS DÉPENDANCES)
        // ====================================================================

        $point->delete();

        return redirect()->route('points.index')
            ->with('success', '✅ Point supprimé avec succès');
    }

    /**
     * API endpoint pour obtenir les détails d'un point
     * Retourne les données au format JSON pour des requêtes AJAX
     * 
     * @param int $id Identifiant du point
     * @return \Illuminate\Http\JsonResponse Données du point avec ses 5 dernières transactions
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si le point n'existe pas
     * 
     * Utilisation possible pour :
     * - Chargement dynamique dans des modals
     * - Mise à jour en temps réel
     * - Intégration avec d'autres systèmes
     */
    public function show($id)
    {
        // ====================================================================
        // 1. RECHERCHE AVEC CHARGEMENT DES RELATIONS RÉCENTES
        // ====================================================================

        $point = Point::with([
            // Les 5 dernières collectes, triées par date décroissante
            'collectes' => function ($q) {
                $q->latest()->limit(5);
            },
            // Les 5 dernières dépenses, triées par date décroissante
            'depenses' => function ($q) {
                $q->latest()->limit(5);
            }
        ])->findOrFail($id);

        // ====================================================================
        // 2. RETOUR DES DONNÉES AU FORMAT JSON
        // ====================================================================

        /**
         * Laravel convertit automatiquement les modèles et relations en JSON
         * La réponse inclura :
         * - Informations du point (id, nom_machine, lieu, status)
         * - Les 5 dernières collectes
         * - Les 5 dernières dépenses
         */
        return response()->json($point);
    }
}