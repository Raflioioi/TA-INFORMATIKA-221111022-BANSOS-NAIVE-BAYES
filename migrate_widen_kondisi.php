<?php
require_once 'config/database.php';

try {
    $pdo->exec("ALTER TABLE warga MODIFY COLUMN kondisi_lantai VARCHAR(100) DEFAULT NULL");
    echo "Widened kondisi_lantai column.\n";
    
    $pdo->exec("ALTER TABLE warga MODIFY COLUMN kondisi_dinding VARCHAR(100) DEFAULT NULL");
    echo "Widened kondisi_dinding column.\n";
    
    $pdo->exec("ALTER TABLE warga MODIFY COLUMN kondisi_atap VARCHAR(100) DEFAULT NULL");
    echo "Widened kondisi_atap column.\n";

    echo "Migration completed successfully!";
} catch (PDOException $e) {
    echo "Error migrating database: " . $e->getMessage();
}
?>
