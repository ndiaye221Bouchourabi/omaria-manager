<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Étape 1 — Convertir les anciens rôles vers les nouveaux
        DB::statement("UPDATE users SET role = 'gestionnaire' WHERE role = 'manager'");
        DB::statement("UPDATE users SET role = 'collecteur'   WHERE role = 'comptable'");

        // Étape 2 — Modifier l'ENUM maintenant que les données sont propres
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('admin','proprietaire','gestionnaire','collecteur')
            NOT NULL DEFAULT 'collecteur'
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'manager'   WHERE role = 'gestionnaire'");
        DB::statement("UPDATE users SET role = 'comptable' WHERE role = 'collecteur'");

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM('admin','manager','comptable')
            NOT NULL DEFAULT 'comptable'
        ");
    }
};