<?php
require_once 'config/database.php';

try {
    // Drop the old column
    $pdo->exec("ALTER TABLE warga DROP COLUMN kondisi_tempat_tinggal");
    echo "Dropped kondisi_tempat_tinggal column.\n";

    // Add new columns
    $pdo->exec("ALTER TABLE warga ADD COLUMN kondisi_lantai VARCHAR(10) DEFAULT 'Layak' AFTER jumlah_tanggungan");
    echo "Added kondisi_lantai column.\n";
    
    $pdo->exec("ALTER TABLE warga ADD COLUMN kondisi_dinding VARCHAR(10) DEFAULT 'Layak' AFTER kondisi_lantai");
    echo "Added kondisi_dinding column.\n";
    
    $pdo->exec("ALTER TABLE warga ADD COLUMN kondisi_atap VARCHAR(10) DEFAULT 'Layak' AFTER kondisi_dinding");
    echo "Added kondisi_atap column.\n";

    echo "Migration completed successfully!";
} catch (PDOException $e) {
    echo "Error migrating database: " . $e->getMessage();
}
?>
