#!/bin/bash

# ============================================================
#  backup.sh — Backup MySQL OMaria → compatible XAMPP/phpMyAdmin
# ============================================================

DATE=$(date +%Y-%m-%d_%H-%M)
FILENAME="backup_omaria_${DATE}.sql"

# Créer le dossier backups s'il n'existe pas
mkdir -p backups

php -r "
// ── Connexion ────────────────────────────────────────────────
\$host   = 'ballast.proxy.rlwy.net';
\$port   = '58024';
\$dbname = 'railway';
\$user   = 'root';
\$pass   = 'enTjQJXEyTNmNQzXjNSpDpzcpeoQMobS';

try {
    \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$dbname;charset=utf8mb4\", \$user, \$pass);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    echo 'Connexion échouée : ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}

// ── En-tête du fichier SQL ───────────────────────────────────
\$sql  = \"-- ============================================================\n\";
\$sql .= \"-- Backup OMaria — $DATE\n\";
\$sql .= \"-- Base    : \$dbname\n\";
\$sql .= \"-- Serveur : \$host:\$port\n\";
\$sql .= \"-- ============================================================\n\n\";

\$sql .= \"SET NAMES utf8mb4;\n\";
\$sql .= \"SET CHARACTER SET utf8mb4;\n\";
\$sql .= \"SET character_set_connection=utf8mb4;\n\";
\$sql .= \"SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\";
\$sql .= \"SET FOREIGN_KEY_CHECKS = 0;\n\";
\$sql .= \"SET AUTOCOMMIT = 0;\n\";
\$sql .= \"START TRANSACTION;\n\n\";

// ── Export table par table ───────────────────────────────────
\$tables = \$pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

foreach (\$tables as \$table) {

    \$sql .= \"-- ------------------------------------------------------------\n\";
    \$sql .= \"-- Table : \$table\n\";
    \$sql .= \"-- ------------------------------------------------------------\n\n\";

    // DROP + CREATE
    \$sql .= \"DROP TABLE IF EXISTS \`\$table\`;\n\";
    \$create = \$pdo->query(\"SHOW CREATE TABLE \`\$table\`\")->fetch(PDO::FETCH_NUM);
    \$sql .= \$create[1] . \";\n\n\";

    // Données
    \$rows = \$pdo->query(\"SELECT * FROM \`\$table\`\")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty(\$rows)) {
        // INSERT par lots de 100 lignes (évite les timeouts phpMyAdmin)
        \$chunks = array_chunk(\$rows, 100);
        foreach (\$chunks as \$chunk) {
            \$columns = '(' . implode(', ', array_map(fn(\$c) => \"\`\$c\`\", array_keys(\$chunk[0]))) . ')';
            \$valueLines = [];
            foreach (\$chunk as \$row) {
                \$values = array_map(function (\$v) use (\$pdo) {
                    return \$v === null ? 'NULL' : \$pdo->quote(\$v);
                }, array_values(\$row));
                \$valueLines[] = '(' . implode(', ', \$values) . ')';
            }
            \$sql .= \"INSERT INTO \`\$table\` \$columns VALUES\n  \";
            \$sql .= implode(\",\n  \", \$valueLines) . \";\n\";
        }
    }

    \$sql .= \"\n\";
}

// ── Pied de page ─────────────────────────────────────────────
\$sql .= \"COMMIT;\n\";
\$sql .= \"SET FOREIGN_KEY_CHECKS = 1;\n\";
\$sql .= \"-- Fin du backup — $DATE\n\";

// ── Écriture du fichier ──────────────────────────────────────
\$path = 'backups/$FILENAME';
if (file_put_contents(\$path, \$sql) !== false) {
    \$size = round(filesize(\$path) / 1024, 1);
    echo \"✅  Backup sauvegardé : \$path (\$size Ko)\n\";
    echo '    Tables exportées : ' . count(\$tables) . PHP_EOL;
    echo '    Lignes totales   : ' . substr_count(\$sql, 'INSERT INTO') . ' blocs' . PHP_EOL;
} else {
    echo '❌  Erreur : impossible d écrire le fichier.' . PHP_EOL;
    exit(1);
}
"