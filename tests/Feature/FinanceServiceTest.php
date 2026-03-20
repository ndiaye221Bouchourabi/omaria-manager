<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Point;
use App\Models\Collecte;
use App\Models\Depense;
use App\Services\FinanceService;
use App\Helpers\FinanceHelper;
use Carbon\Carbon;

/**
 * Tests unitaires pour le service financier
 * Vérifie la précision des calculs à 3 décimales
 * Assure la fiabilité des formules de rentabilité
 * 
 * @package Tests\Feature
 */
class FinanceServiceTest extends TestCase
{
    /**
     * Instance du service à tester
     * @var FinanceService
     */
    protected $financeService;

    /**
     * Configuration initiale avant chaque test
     * S'exécute automatiquement avant chaque méthode de test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Création d'une nouvelle instance du service pour chaque test
        // Cela garantit l'isolation entre les tests
        $this->financeService = new FinanceService();
    }

    /**
     * Test : Calcul de la charge globale par point avec précision 3 décimales
     * 
     * Scénario :
     * - 1 dépense globale de 1000.567 FCFA
     * - 3 points actifs
     * - Vérifie que la charge par point = 1000.567 / 3 = 333.522 (arrondi à 3 décimales)
     * 
     * @test
     */
    public function it_calculate_global_charge_per_point_with_3_decimals()
    {
        // ====================================================================
        // 1. PRÉPARATION DES DONNÉES DE TEST (ARRANGEMENT)
        // ====================================================================

        // Création d'une dépense globale avec 3 décimales
        Depense::factory()->create([
            'portee' => 'globale',
            'montant' => 1000.567,           // Montant avec 3 décimales
            'date_depense' => Carbon::now()
        ]);

        // Création de 3 points actifs
        Point::factory()->count(3)->create([
            'status' => 'Actif'               // Statut actif obligatoire
        ]);

        // Définition de la période d'analyse (mois en cours)
        $dateDebut = Carbon::now()->startOfMonth();
        $dateFin = Carbon::now()->endOfMonth();

        // ====================================================================
        // 2. EXÉCUTION DE LA MÉTHODE À TESTER (ACT)
        // ====================================================================

        $result = $this->financeService->calculateGlobalChargePerPoint($dateDebut, $dateFin);

        // ====================================================================
        // 3. VÉRIFICATIONS DES RÉSULTATS (ASSERT)
        // ====================================================================

        // Calcul attendu : 1000.567 / 3 = 333.522333... arrondi à 333.522
        $expectedParPoint = round(1000.567 / 3, 3);

        // Vérification du total des dépenses globales
        $this->assertEquals(1000.567, $result['total']);

        // Vérification du nombre de points actifs
        $this->assertEquals(3, $result['points_actifs']);

        /**
         * assertEqualsWithDelta permet une marge d'erreur (delta)
         * Utile pour les calculs flottants
         * Ici on accepte une erreur de 0.001 (1/1000ème)
         */
        $this->assertEqualsWithDelta($expectedParPoint, $result['par_point'], 0.001);
    }

    /**
     * Test : Calcul de la rentabilité d'un point avec haute précision
     * 
     * Scénario complet :
     * - Point A : collecte 1500.345, dépenses directes 450.123
     * - Dépense globale : 900.456 répartie sur 3 points actifs
     * - Charge globale pour le point : 900.456 / 3 = 300.152
     * - Bénéfice attendu : 1500.345 - (450.123 + 300.152) = 750.070
     * 
     * @test
     */
    public function it_calculate_point_profitability_with_high_precision()
    {
        // ====================================================================
        // 1. CRÉATION DU POINT PRINCIPAL
        // ====================================================================

        $point = Point::factory()->create(['status' => 'Actif']);

        // ====================================================================
        // 2. CRÉATION DES TRANSACTIONS POUR CE POINT
        // ====================================================================

        // Collecte avec 3 décimales
        Collecte::factory()->for($point)->create([
            'montant' => 1500.345,
            'date_collecte' => Carbon::now()
        ]);

        // Dépense directe avec 3 décimales
        Depense::factory()->for($point)->create([
            'portee' => 'point',
            'montant' => 450.123,
            'date_depense' => Carbon::now()
        ]);

        // ====================================================================
        // 3. CRÉATION DES DONNÉES GLOBALES
        // ====================================================================

        // Dépense globale
        Depense::factory()->create([
            'portee' => 'globale',
            'montant' => 900.456,
            'date_depense' => Carbon::now()
        ]);

        // Autres points actifs pour répartir la charge globale (total = 3 points)
        Point::factory()->count(2)->create(['status' => 'Actif']);

        // ====================================================================
        // 4. EXÉCUTION DES CALCULS
        // ====================================================================

        $dateDebut = Carbon::now()->startOfMonth();
        $dateFin = Carbon::now()->endOfMonth();

        // Étape 1 : Calcul de la charge globale par point
        $globalData = $this->financeService->calculateGlobalChargePerPoint($dateDebut, $dateFin);

        // Étape 2 : Calcul de la rentabilité du point
        $result = $this->financeService->calculatePointProfitability(
            $point,
            $dateDebut,
            $dateFin,
            $globalData['par_point']
        );

        // ====================================================================
        // 5. CALCUL MANUEL POUR VALIDATION
        // ====================================================================

        $chargeGlobale = round(900.456 / 3, 3);          // 300.152
        $beneficeAttendu = round(1500.345 - (450.123 + $chargeGlobale), 3); // 750.070

        // ====================================================================
        // 6. VÉRIFICATIONS AVEC MARGE D'ERREUR
        // ====================================================================

        $this->assertEqualsWithDelta(1500.345, $result['collectes'], 0.001);
        $this->assertEqualsWithDelta(450.123, $result['depenses_directes'], 0.001);
        $this->assertEqualsWithDelta($chargeGlobale, $result['charge_globale'], 0.001);
        $this->assertEqualsWithDelta($beneficeAttendu, $result['benefice'], 0.001);
    }

    /**
     * Test : Formatage des montants avec 3 décimales
     * Vérifie que le helper FinanceHelper formate correctement
     * les nombres avec le format français (espaces, virgule)
     * 
     * Format attendu : "X XXX XXX,XXX FCFA"
     * Exemple : 1234567.891 → "1 234 567,891 FCFA"
     * 
     * @test
     */
    public function it_formats_money_correctly()
    {
        // ====================================================================
        // 1. PRÉPARATION
        // ====================================================================

        $amount = 1234567.891;  // Montant avec 3 décimales

        // ====================================================================
        // 2. EXÉCUTION DU FORMATAGE
        // ====================================================================

        $formatted = FinanceHelper::formatMoney($amount);

        // ====================================================================
        // 3. VÉRIFICATION DU FORMAT
        // ====================================================================

        /**
         * Vérifications :
         * - Espaces comme séparateurs de milliers : "1 234 567"
         * - Virgule pour les décimales : ",891"
         * - Devise "FCFA" à la fin
         */
        $this->assertEquals('1 234 567,891 FCFA', $formatted);
    }
}