<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * OMariaSeeder — Données réalistes 3 mois (Avril → Juin 2026)
 *
 * Structure :
 * - 5 points d'eau actifs + 1 inactif
 * - Collectes hebdomadaires par point (champ `semaine` = "S{W}-{YYYY}")
 * - Dépenses par point (réparations, entretien…)
 * - Dépenses globales en fin de mois (salaires, loyer, carburant…)
 *
 * Pour lancer : php artisan db:seed --class=OMariaSeeder
 */
class OMariaSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 0. NETTOYAGE
        // ============================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('collectes')->truncate();
        DB::table('depenses')->truncate();
        DB::table('points')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ============================================================
        // 1. POINTS D'EAU
        // ============================================================
        $points = [
            ['id' => 1, 'nom_machine' => 'Machine 1', 'lieu' => 'Marché Sandaga',       'status' => 'Actif'],
            ['id' => 2, 'nom_machine' => 'Machine 2', 'lieu' => 'Quartier Médina',      'status' => 'Actif'],
            ['id' => 3, 'nom_machine' => 'Machine 3', 'lieu' => 'Pikine Centre',        'status' => 'Actif'],
            ['id' => 4, 'nom_machine' => 'Machine 4', 'lieu' => 'Guédiawaye',           'status' => 'Actif'],
            ['id' => 5, 'nom_machine' => 'Machine 5', 'lieu' => 'Parcelles Assainies',  'status' => 'Actif'],
            ['id' => 6, 'nom_machine' => 'Machine 6', 'lieu' => 'Rufisque Est',         'status' => 'Inactif'],
        ];

        foreach ($points as $p) {
            DB::table('points')->insert([
                'id'          => $p['id'],
                'nom_machine' => $p['nom_machine'],
                'lieu'        => $p['lieu'],
                'status'      => $p['status'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $actifIds = [1, 2, 3, 4, 5]; // Machine 6 inactive

        // ============================================================
        // 2. DÉFINITION DES SEMAINES PAR MOIS
        //    On couvre Avril, Mai, Juin 2026
        // ============================================================

        /*
         * Format semaine : "S{numéro ISO}-{année}"
         * Chaque semaine : lundi → dimanche
         *
         * Avril 2026
         *   S14 : 30 mars  → 5 avril    (on garde la date_collecte dans avril)
         *   S15 : 6  → 12 avril
         *   S16 : 13 → 19 avril
         *   S17 : 20 → 26 avril
         *
         * Mai 2026
         *   S18 : 27 avril → 3 mai
         *   S19 : 4  → 10 mai
         *   S20 : 11 → 17 mai
         *   S21 : 18 → 24 mai
         *   S22 : 25 → 31 mai
         *
         * Juin 2026
         *   S23 : 1  → 7  juin
         *   S24 : 8  → 14 juin
         *   S25 : 15 → 21 juin
         *   S26 : 22 → 28 juin
         */

        $semaines = [
            // [ label,    date_collecte représentative (mercredi de la semaine) ]
            ['S14-2026', '2026-04-01'],
            ['S15-2026', '2026-04-08'],
            ['S16-2026', '2026-04-15'],
            ['S17-2026', '2026-04-23'],
            ['S18-2026', '2026-04-29'],
            ['S19-2026', '2026-05-07'],
            ['S20-2026', '2026-05-13'],
            ['S21-2026', '2026-05-21'],
            ['S22-2026', '2026-05-28'],
            ['S23-2026', '2026-06-03'],
            ['S24-2026', '2026-06-10'],
            ['S25-2026', '2026-06-18'],
            ['S26-2026', '2026-06-25'],
        ];

        // ============================================================
        // 3. REVENUS DE BASE PAR MACHINE (montant hebdo moyen en FCFA)
        //    Variation réaliste selon la machine et la saison
        // ============================================================
        $baseRevenu = [
            1 => 22000,   // Machine 1 — Sandaga (marché animé mais vieux matériel)
            2 => 19500,   // Machine 2 — Médina
            3 => 51000,   // Machine 3 — Pikine (meilleure zone)
            4 => 40500,   // Machine 4 — Guédiawaye
            5 => 49000,   // Machine 5 — Parcelles (forte densité)
        ];

        // Coefficients saisonniers par semaine (chaleur = plus de ventes)
        $saisonCoef = [
            'S14-2026' => 0.88,
            'S15-2026' => 0.92,
            'S16-2026' => 0.95,
            'S17-2026' => 0.97,
            'S18-2026' => 1.00, // Début mai — montée
            'S19-2026' => 1.05,
            'S20-2026' => 1.08,
            'S21-2026' => 1.10, // Pic chaleur
            'S22-2026' => 1.12,
            'S23-2026' => 1.15, // Juin — saison chaude
            'S24-2026' => 1.13,
            'S25-2026' => 1.10,
            'S26-2026' => 1.08,
        ];

        // ============================================================
        // 4. COLLECTES HEBDOMADAIRES
        // ============================================================
        $collecteId = 1;

        foreach ($semaines as [$semLabel, $dateStr]) {
            foreach ($actifIds as $pointId) {
                $base = $baseRevenu[$pointId];
                $coef = $saisonCoef[$semLabel];

                // Légère variation aléatoire déterministe (pas de rand() pour reproductibilité)
                $hash  = crc32($semLabel . $pointId);
                $delta = (($hash % 15) - 7) / 100; // entre -7% et +7%

                $montant = round($base * $coef * (1 + $delta));

                // Machine 1 creuse en S17 (panne légère, simulée)
                if ($pointId === 1 && $semLabel === 'S17-2026') {
                    $montant = round($montant * 0.55);
                }

                DB::table('collectes')->insert([
                    'id'            => $collecteId++,
                    'point_id'      => $pointId,
                    'semaine'       => $semLabel,
                    'date_collecte' => $dateStr,
                    'montant'       => $montant,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // ============================================================
        // 5. DÉPENSES PAR POINT (portée = 'point')
        //    Réparations, pièces, entretien courant
        // ============================================================
        $depId = 1;

        $depensesPoints = [
            // Avril
            ['2026-04-05', 1, 'Réparation robinet',       'Entretien',  3200],
            ['2026-04-10', 2, 'Remplacement filtre',       'Pièces',     5500],
            ['2026-04-15', 3, 'Nettoyage cuve',            'Entretien',  2800],
            ['2026-04-18', 1, 'Panne moteur partielle',    'Réparation', 8700],  // cause baisse S17
            ['2026-04-22', 4, 'Entretien préventif',       'Entretien',  3100],
            ['2026-04-28', 5, 'Remplacement joint',        'Pièces',     1900],

            // Mai
            ['2026-05-03', 2, 'Réparation tuyauterie',     'Réparation', 6200],
            ['2026-05-08', 3, 'Changement pompe',          'Pièces',     12500],
            ['2026-05-14', 1, 'Entretien robinets',        'Entretien',  2400],
            ['2026-05-19', 4, 'Désinfection cuve',         'Entretien',  3500],
            ['2026-05-22', 5, 'Remplacement compteur',     'Pièces',     7800],
            ['2026-05-27', 2, 'Graissage mécanismes',      'Entretien',  1500],

            // Juin
            ['2026-06-04', 3, 'Nettoyage profond',         'Entretien',  3800],
            ['2026-06-09', 1, 'Réparation vanne',          'Réparation', 5100],
            ['2026-06-12', 4, 'Remplacement filtre',       'Pièces',     4900],
            ['2026-06-17', 5, 'Entretien mensuel',         'Entretien',  2600],
            ['2026-06-20', 2, 'Réparation fuite',          'Réparation', 4300],
            ['2026-06-25', 3, 'Remplacement joint pompe',  'Pièces',     3700],
        ];

        foreach ($depensesPoints as [$date, $ptId, $desc, $type, $montant]) {
            DB::table('depenses')->insert([
                'id'            => $depId++,
                'point_id'      => $ptId,
                'type_depense'  => $type,
                'portee'        => 'point',
                'description'   => $desc,
                'date_depense'  => $date,
                'montant'       => $montant,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // ============================================================
        // 6. DÉPENSES GLOBALES (portée = 'globale') — fin de mois
        //    Salaires, loyer, carburant, communication
        // ============================================================
        $depensesGlobales = [
            // Avril 2026 (fin de mois : 30 avril)
            ['2026-04-30', 'Salaires',      'Salaire',     'Salaires 3 agents avril 2026',          75000],
            ['2026-04-30', 'Loyer',         'Loyer',       'Loyer local stockage avril',             30000],
            ['2026-04-30', 'Carburant',     'Transport',   'Carburant tournée collecte avril',       18500],
            ['2026-04-30', 'Communication', 'Frais divers','Crédit téléphonique + internet avril',    8000],
            ['2026-04-30', 'Fournitures',   'Frais divers','Produits entretien & désinfection',       6500],

            // Mai 2026 (fin de mois : 31 mai)
            ['2026-05-31', 'Salaires',      'Salaire',     'Salaires 3 agents mai 2026',            75000],
            ['2026-05-31', 'Loyer',         'Loyer',       'Loyer local stockage mai',               30000],
            ['2026-05-31', 'Carburant',     'Transport',   'Carburant tournée collecte mai',         21000],
            ['2026-05-31', 'Communication', 'Frais divers','Crédit téléphonique + internet mai',      8000],
            ['2026-05-31', 'Fournitures',   'Frais divers','Produits entretien & désinfection mai',   7200],
            ['2026-05-31', 'Assurance',     'Frais divers','Assurance équipements — semestrielle',   15000],

            // Juin 2026 (fin de mois : 30 juin)
            ['2026-06-30', 'Salaires',      'Salaire',     'Salaires 3 agents juin 2026',           78000],  // légère augmentation
            ['2026-06-30', 'Loyer',         'Loyer',       'Loyer local stockage juin',              30000],
            ['2026-06-30', 'Carburant',     'Transport',   'Carburant tournée collecte juin',        22500],
            ['2026-06-30', 'Communication', 'Frais divers','Crédit téléphonique + internet juin',     8000],
            ['2026-06-30', 'Fournitures',   'Frais divers','Produits entretien & désinfection juin',  6800],
        ];

        foreach ($depensesGlobales as [$date, $typeD, $cat, $desc, $montant]) {
            DB::table('depenses')->insert([
                'id'            => $depId++,
                'point_id'      => null,
                'type_depense'  => $typeD,
                'portee'        => 'globale',
                'description'   => $desc,
                'date_depense'  => $date,
                'montant'       => $montant,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // ============================================================
        // 7. RÉSUMÉ CONSOLE
        // ============================================================
        $this->command->info('');
        $this->command->info('✅  OMariaSeeder — Données insérées avec succès');
        $this->command->info('');
        $this->command->table(
            ['Élément', 'Quantité'],
            [
                ['Points d\'eau',       count($points)],
                ['Points actifs',       count($actifIds)],
                ['Semaines couvertes',  count($semaines)],
                ['Collectes',           ($collecteId - 1)],
                ['Dépenses points',     count($depensesPoints)],
                ['Dépenses globales',   count($depensesGlobales)],
                ['Période',             'Avril → Juin 2026'],
            ]
        );
        $this->command->info('');

        // Aperçu des totaux par mois
        $mois = [
            'Avril 2026' => ['2026-04-01', '2026-04-30'],
            'Mai 2026'   => ['2026-05-01', '2026-05-31'],
            'Juin 2026'  => ['2026-06-01', '2026-06-30'],
        ];

        foreach ($mois as $label => [$debut, $fin]) {
            $totalC = DB::table('collectes')
                ->whereBetween('date_collecte', [$debut, $fin])
                ->sum('montant');
            $totalD = DB::table('depenses')
                ->whereBetween('date_depense', [$debut, $fin])
                ->sum('montant');
            $this->command->line(
                sprintf('  📅 %-12s  Collectes: %s FCFA  |  Dépenses: %s FCFA  |  Bénéfice: %s FCFA',
                    $label,
                    number_format($totalC, 0, ',', ' '),
                    number_format($totalD, 0, ',', ' '),
                    number_format($totalC - $totalD, 0, ',', ' ')
                )
            );
        }

        $this->command->info('');
        $this->command->info('  💡 Commande : php artisan db:seed --class=OMariaSeeder');
        $this->command->info('');
    }
}
