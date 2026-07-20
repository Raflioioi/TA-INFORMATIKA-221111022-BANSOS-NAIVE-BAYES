<?php
require_once 'c:/xampp/htdocs/bansos-app/config/database.php';
try {
    $pdo->exec("ALTER TABLE warga MODIFY penghasilan VARCHAR(50) NOT NULL");
    $pdo->exec("ALTER TABLE warga MODIFY jumlah_tanggungan VARCHAR(50) NOT NULL");
    echo "Database altered successfully.";
} catch (PDOException $e) {
    echo "Failed to alter DB: " . $e->getMessage();
}
