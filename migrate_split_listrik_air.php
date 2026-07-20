<?php
require_once 'config/database.php';

try {
    $pdo->exec("ALTER TABLE warga DROP COLUMN akses_listrik_air");
    $pdo->exec("ALTER TABLE warga ADD COLUMN akses_listrik VARCHAR(100) DEFAULT NULL AFTER pengeluaran_bulanan");
    $pdo->exec("ALTER TABLE warga ADD COLUMN akses_air VARCHAR(100) DEFAULT NULL AFTER akses_listrik");

    echo "Migration completed successfully!";
} catch (PDOException $e) {
    echo "Error migrating database: " . $e->getMessage();
}
?>
