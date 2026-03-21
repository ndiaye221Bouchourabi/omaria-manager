#!/bin/bash
DATE=$(date +%Y-%m-%d_%H-%M)
FILENAME="backup_omaria_${DATE}.sql"

php -r "
\$pdo = new PDO('mysql:host=ballast.proxy.rlwy.net;port=58024;dbname=railway', 'root', 'enTjQJXEyTNmNQzXjNSpDpzcpeoQMobS');
\$tables = \$pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
\$sql = '-- Backup OMaria $DATE\n\n';
foreach(\$tables as \$table) {
    \$create = \$pdo->query(\"SHOW CREATE TABLE \$table\")->fetch();
    \$sql .= \$create[1] . \";\n\n\";
    \$rows = \$pdo->query(\"SELECT * FROM \$table\")->fetchAll(PDO::FETCH_ASSOC);
    foreach(\$rows as \$row) {
        \$values = array_map(function(\$v) use (\$pdo) { return \$v === null ? 'NULL' : \$pdo->quote(\$v); }, \$row);
        \$sql .= \"INSERT INTO \$table VALUES (\" . implode(',', \$values) . \");\n\";
    }
    \$sql .= \"\n\";
}
file_put_contents('backups/$FILENAME', \$sql);
echo 'Backup saved: backups/$FILENAME\n';
"
