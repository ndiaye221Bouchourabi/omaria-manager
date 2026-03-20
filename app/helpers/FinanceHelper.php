<?php

namespace App\Helpers;

class FinanceHelper
{
    /**
     * Formate un montant intelligemment
     * - Entier → sans décimales
     * - Décimal → avec le nombre de décimales spécifié
     */
    public static function formatMoney($amount, $decimals = 3, $currency = 'FCFA')
    {
        if ($amount === null || $amount === '') {
            $amount = 0;
        }

        // Vérifier si c'est un entier (pas de décimales significatives)
        if (floor($amount) == $amount) {
            // C'est un entier, on ignore le paramètre $decimals
            return number_format($amount, 0, ',', ' ') . ' ' . $currency;
        } else {
            // C'est un décimal, on utilise le nombre de décimales spécifié
            return number_format($amount, $decimals, ',', ' ') . ' ' . $currency;
        }
    }

    /**
     * Arrondi à 3 décimales pour les calculs
     */
    public static function roundMoney($amount)
    {
        return round($amount, 3);
    }
}